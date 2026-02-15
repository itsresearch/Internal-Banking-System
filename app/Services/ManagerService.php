<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transfer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionNotificationService;

class ManagerService
{
    private TransactionNotificationService $notifier;

    public function __construct(TransactionNotificationService $notifier)
    {
        $this->notifier = $notifier;
    }
    public function dailySummary(Carbon $start, Carbon $end): array
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->endOfDay();

        $totalDeposits = Transaction::where('status', 'approved')
            ->where('transaction_type', 'deposit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $totalWithdrawals = Transaction::where('status', 'approved')
            ->where('transaction_type', 'withdrawal')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $totalTransfers = Transfer::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $cashByStaff = Transaction::select('created_by', DB::raw('SUM(amount) as total_amount'))
            ->where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('transaction_type', ['deposit', 'withdrawal'])
            ->groupBy('created_by')
            ->with('createdBy')
            ->get();

        $seriesRows = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $seriesLabels = $seriesRows->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('M d');
        })->values();

        $seriesTotals = $seriesRows->pluck('total_amount')->map(fn ($val) => (float) $val)->values();

        return [
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalTransfers' => $totalTransfers,
            'cashByStaff' => $cashByStaff,
            'seriesLabels' => $seriesLabels,
            'seriesTotals' => $seriesTotals,
        ];
    }

    public function reverseTransaction(Transaction $transaction, int $managerId, string $reason): void
    {
        if ($transaction->is_reversal || $transaction->reversal_of) {
            throw new \RuntimeException('This transaction has already been reversed.');
        }

        if ($transaction->status !== 'approved') {
            throw new \RuntimeException('Only approved transactions can be reversed.');
        }

        DB::transaction(function () use ($transaction, $managerId, $reason) {
            if ($transaction->transaction_type === 'transfer') {
                $this->reverseTransfer($transaction, $managerId, $reason);
                return;
            }

            $customer = Customer::lockForUpdate()->findOrFail($transaction->customer_id);
            $amount = (float) $transaction->amount;
            $balanceBefore = (float) $customer->balance;
            $reverseType = $transaction->transaction_type === 'deposit' ? 'withdrawal' : 'deposit';
            $balanceAfter = $reverseType === 'withdrawal'
                ? $balanceBefore - $amount
                : $balanceBefore + $amount;

            if ($reverseType === 'withdrawal') {
                $this->ensureDebitAllowed($customer, $balanceAfter, 'Insufficient balance for reversal.');
            }

            $reverse = $customer->transactions()->create([
                'transaction_type' => $reverseType,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'approved',
                'reference_number' => $this->generateReferenceNumber(),
                'created_by' => $managerId,
                'approved_by' => $managerId,
                'is_reversal' => true,
                'reversal_of' => $transaction->id,
                'reversal_reason' => $reason,
                'notes' => 'Reversal entry',
            ]);

            $customer->update(['balance' => $balanceAfter]);
            $this->notifier->notify($reverse);

            $reverse->refresh();
        });
    }


    private function reverseTransfer(Transaction $transaction, int $managerId, string $reason): void
    {
        $linked = $transaction->linkedTransaction;
        if (!$linked) {
            $linked = Transaction::find($transaction->linked_transaction_id);
        }

        if (!$linked) {
            throw new \RuntimeException('Linked transfer transaction not found.');
        }

        $debit = $transaction->balance_after < $transaction->balance_before ? $transaction : $linked;
        $credit = $debit->id === $transaction->id ? $linked : $transaction;

        $fromCustomer = Customer::lockForUpdate()->findOrFail($credit->customer_id);
        $toCustomer = Customer::lockForUpdate()->findOrFail($debit->customer_id);

        $amount = (float) $debit->amount;

        $fromBalanceBefore = (float) $fromCustomer->balance;
        $fromBalanceAfter = $fromBalanceBefore - $amount;
        $this->ensureDebitAllowed($fromCustomer, $fromBalanceAfter, 'Insufficient balance for transfer reversal.');

        $toBalanceBefore = (float) $toCustomer->balance;
        $toBalanceAfter = $toBalanceBefore + $amount;

        $debitRef = $this->generateReferenceNumber();
        $creditRef = $this->generateReferenceNumber();

        $debitReverse = $fromCustomer->transactions()->create([
            'transaction_type' => 'transfer',
            'amount' => $amount,
            'balance_before' => $fromBalanceBefore,
            'balance_after' => $fromBalanceAfter,
            'status' => 'approved',
            'reference_number' => $debitRef,
            'created_by' => $managerId,
            'approved_by' => $managerId,
            'is_reversal' => true,
            'reversal_of' => $credit->id,
            'reversal_reason' => $reason,
            'notes' => 'Transfer reversal (debit)',
        ]);

        $creditReverse = $toCustomer->transactions()->create([
            'transaction_type' => 'transfer',
            'amount' => $amount,
            'balance_before' => $toBalanceBefore,
            'balance_after' => $toBalanceAfter,
            'status' => 'approved',
            'reference_number' => $creditRef,
            'linked_transaction_id' => $debitReverse->id,
            'created_by' => $managerId,
            'approved_by' => $managerId,
            'is_reversal' => true,
            'reversal_of' => $debit->id,
            'reversal_reason' => $reason,
            'notes' => 'Transfer reversal (credit)',
        ]);

        $debitReverse->update(['linked_transaction_id' => $creditReverse->id]);

        $fromCustomer->update(['balance' => $fromBalanceAfter]);
        $toCustomer->update(['balance' => $toBalanceAfter]);

        $this->notifier->notify($debitReverse);
        $this->notifier->notify($creditReverse);
    }

    private function ensureDebitAllowed(Customer $customer, float $balanceAfter, string $message): void
    {
        if ($balanceAfter < 0) {
            throw new \RuntimeException($message);
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
