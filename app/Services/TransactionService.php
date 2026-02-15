<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionNotificationService;

class TransactionService
{
    private int $approvalLimit = 2000000;
    private TransactionNotificationService $notifier;

    public function __construct(TransactionNotificationService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function deposit(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);
            $this->ensureCustomerActive($customer);

            $balanceBefore = $customer->balance;
            $amount = $data['amount'];
            $balanceAfter = $balanceBefore + $amount;

            $transaction = $customer->transactions()->create([
                'transaction_type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'approved',
                'reference_number' => $this->generateReferenceNumber(),
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $customer->update(['balance' => $balanceAfter]);

            $this->notifier->notify($transaction);

            return $transaction;
        });
    }

    public function withdraw(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);
            $this->ensureCustomerActive($customer);

            $balanceBefore = $customer->balance;
            $amount = $data['amount'];
            $balanceAfter = $balanceBefore - $amount;

            if ($balanceAfter < 0) {
                throw new \RuntimeException('Insufficient balance.');
            }

            $requiresApproval = false;
            $status = 'approved';

            $transaction = $customer->transactions()->create([
                'transaction_type' => 'withdrawal',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => $status,
                'reference_number' => $this->generateReferenceNumber(),
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $customer->update(['balance' => $balanceAfter]);

            $this->notifier->notify($transaction);

            return [
                'transaction' => $transaction,
                'requiresApproval' => $requiresApproval,
            ];
        });
    }

    public function transfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $fromCustomer = Customer::findOrFail($data['from_customer_id']);
            $toCustomer = Customer::findOrFail($data['to_customer_id']);
            $this->ensureCustomerActive($fromCustomer);
            $this->ensureCustomerActive($toCustomer);

            $amount = $data['amount'];
            $referenceNumber = $this->generateTransferReferenceNumber();

            $fromBalanceBefore = $fromCustomer->balance;
            $fromBalanceAfter = $fromBalanceBefore - $amount;

            if ($fromBalanceAfter < 0) {
                throw new \RuntimeException('Insufficient balance in source account.');
            }

            $requiresApproval = $amount > $this->approvalLimit;
            $status = $requiresApproval ? 'pending' : 'approved';

            $toBalanceBefore = $toCustomer->balance;
            $toBalanceAfter = $toBalanceBefore + $amount;

            $transfer = Transfer::create([
                'from_customer_id' => $fromCustomer->id,
                'to_customer_id' => $toCustomer->id,
                'amount' => $amount,
                'from_balance_before' => $fromBalanceBefore,
                'from_balance_after' => $fromBalanceAfter,
                'to_balance_before' => $toBalanceBefore,
                'to_balance_after' => $toBalanceAfter,
                'status' => $status,
                'reference_number' => $referenceNumber,
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($status === 'approved') {
                $fromCustomer->update(['balance' => $fromBalanceAfter]);
                $toCustomer->update(['balance' => $toBalanceAfter]);
            }

            return [
                'transfer' => $transfer,
                'requiresApproval' => $requiresApproval,
            ];
        });
    }

    public function approve(Transaction $transaction, int $approverId): void
    {
        DB::transaction(function () use ($transaction, $approverId) {
            if ($transaction->status !== 'pending') {
                throw new \RuntimeException('Transaction is not pending.');
            }

            $transaction->update([
                'status' => 'approved',
                'approved_by' => $approverId,
            ]);

            $customer = $transaction->customer;
            $customer->update(['balance' => $transaction->balance_after]);

            $this->notifier->notify($transaction);

        });
    }

    public function reject(Transaction $transaction, int $approverId, string $reason): void
    {
        if ($transaction->status !== 'pending') {
            throw new \RuntimeException('Transaction is not pending.');
        }

        $transaction->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'rejected_reason' => $reason,
        ]);

    }

    public function approveTransfer(Transfer $transfer, int $approverId): void
    {
        DB::transaction(function () use ($transfer, $approverId) {
            if ($transfer->status !== 'pending') {
                throw new \RuntimeException('Transfer is not pending.');
            }

            $transfer->update([
                'status' => 'approved',
                'approved_by' => $approverId,
            ]);

            $fromCustomer = Customer::findOrFail($transfer->from_customer_id);
            $toCustomer = Customer::findOrFail($transfer->to_customer_id);

            $fromCustomer->update(['balance' => $transfer->from_balance_after]);
            $toCustomer->update(['balance' => $transfer->to_balance_after]);
        });
    }

    public function rejectTransfer(Transfer $transfer, int $approverId, string $reason): void
    {
        if ($transfer->status !== 'pending') {
            throw new \RuntimeException('Transfer is not pending.');
        }

        $transfer->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'rejected_reason' => $reason,
        ]);
    }

    private function generateReferenceNumber(): string
    {
        $prefix = 'TXN-' . date('Ymd');
        do {
            $suffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $referenceNumber = $prefix . $suffix;
        } while (Transaction::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }

    private function generateTransferReferenceNumber(): string
    {
        $prefix = 'TRF-' . date('Ymd');
        do {
            $suffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $referenceNumber = $prefix . $suffix;
        } while (Transfer::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }

    private function ensureCustomerActive(Customer $customer): void
    {
        if ($customer->status !== 'active') {
            throw new \RuntimeException('Account is not active.');
        }
        if ($customer->is_frozen) {
            throw new \RuntimeException('Account is frozen.');
        }
    }
}
