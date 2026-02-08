<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    private int $approvalLimit = 100000;

    public function deposit(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);

            if ($customer->status !== 'active') {
                throw new \RuntimeException('Account is not active.');
            }

            $balanceBefore = $customer->opening_balance;
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

            $customer->update(['opening_balance' => $balanceAfter]);

            return $transaction;
        });
    }

    public function withdraw(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);

            if ($customer->status !== 'active') {
                throw new \RuntimeException('Account is not active.');
            }

            $balanceBefore = $customer->opening_balance;
            $amount = $data['amount'];
            $balanceAfter = $balanceBefore - $amount;

            if ($customer->account_type === 'savings') {
                if ($balanceAfter < 0) {
                    throw new \RuntimeException('Insufficient balance for withdrawal.');
                }
            } else {
                if ($customer->overdraft_enabled && $customer->overdraft_limit) {
                    if ($balanceAfter < -$customer->overdraft_limit) {
                        throw new \RuntimeException('Overdraft limit exceeded.');
                    }
                } else {
                    if ($balanceAfter < 0) {
                        throw new \RuntimeException('Insufficient balance.');
                    }
                }
            }

            $requiresApproval = $amount > $this->approvalLimit;
            $status = $requiresApproval ? 'pending' : 'approved';

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

            if ($status === 'approved') {
                $customer->update(['opening_balance' => $balanceAfter]);
            }

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

            if ($fromCustomer->status !== 'active' || $toCustomer->status !== 'active') {
                throw new \RuntimeException('One or both accounts are not active.');
            }

            $amount = $data['amount'];
            $debitReferenceNumber = $this->generateReferenceNumber();
            $creditReferenceNumber = $this->generateReferenceNumber();

            $fromBalanceBefore = $fromCustomer->opening_balance;
            $fromBalanceAfter = $fromBalanceBefore - $amount;

            if ($fromCustomer->account_type === 'savings') {
                if ($fromBalanceAfter < 0) {
                    throw new \RuntimeException('Insufficient balance in source account.');
                }
            } else {
                if ($fromCustomer->overdraft_enabled && $fromCustomer->overdraft_limit) {
                    if ($fromBalanceAfter < -$fromCustomer->overdraft_limit) {
                        throw new \RuntimeException('Overdraft limit exceeded in source account.');
                    }
                } else {
                    if ($fromBalanceAfter < 0) {
                        throw new \RuntimeException('Insufficient balance in source account.');
                    }
                }
            }

            $requiresApproval = $amount > $this->approvalLimit;
            $status = $requiresApproval ? 'pending' : 'approved';

            $debitTransaction = $fromCustomer->transactions()->create([
                'transaction_type' => 'transfer',
                'amount' => $amount,
                'balance_before' => $fromBalanceBefore,
                'balance_after' => $fromBalanceAfter,
                'status' => $status,
                'reference_number' => $debitReferenceNumber,
                'created_by' => auth()->id(),
                'notes' => "Transfer to {$toCustomer->first_name} {$toCustomer->last_name}. " . ($data['notes'] ?? ''),
            ]);

            $toBalanceBefore = $toCustomer->opening_balance;
            $toBalanceAfter = $toBalanceBefore + $amount;

            $creditTransaction = $toCustomer->transactions()->create([
                'transaction_type' => 'transfer',
                'amount' => $amount,
                'balance_before' => $toBalanceBefore,
                'balance_after' => $toBalanceAfter,
                'status' => $status,
                'reference_number' => $creditReferenceNumber,
                'linked_transaction_id' => $debitTransaction->id,
                'created_by' => auth()->id(),
                'notes' => "Transfer from {$fromCustomer->first_name} {$fromCustomer->last_name}. " . ($data['notes'] ?? ''),
            ]);

            $debitTransaction->update(['linked_transaction_id' => $creditTransaction->id]);

            if ($status === 'approved') {
                $fromCustomer->update(['opening_balance' => $fromBalanceAfter]);
                $toCustomer->update(['opening_balance' => $toBalanceAfter]);
            }

            return [
                'transaction' => $debitTransaction,
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
            $customer->update(['opening_balance' => $transaction->balance_after]);

            if ($transaction->transaction_type === 'transfer' && $transaction->linked_transaction_id) {
                $linkedTransaction = Transaction::find($transaction->linked_transaction_id);
                if ($linkedTransaction) {
                    $linkedTransaction->update([
                        'status' => 'approved',
                        'approved_by' => $approverId,
                    ]);
                    $linkedCustomer = $linkedTransaction->customer;
                    $linkedCustomer->update(['opening_balance' => $linkedTransaction->balance_after]);
                }
            }
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

        if ($transaction->transaction_type === 'transfer' && $transaction->linked_transaction_id) {
            $linkedTransaction = Transaction::find($transaction->linked_transaction_id);
            if ($linkedTransaction) {
                $linkedTransaction->update([
                    'status' => 'rejected',
                    'approved_by' => $approverId,
                    'rejected_reason' => "Linked transaction rejected: {$reason}",
                ]);
            }
        }
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
}
