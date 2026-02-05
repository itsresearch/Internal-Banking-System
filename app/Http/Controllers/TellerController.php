<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TellerController extends Controller
{
    private $approvalLimit = 100000; // Transactions above this require approval

    // ============ Deposit ============
    public function depositForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.deposit', compact('customers'));
    }

    public function storeDeposit(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:10',
            'notes'       => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $customer = Customer::findOrFail($validated['customer_id']);

                // Validate account status
                if ($customer->status !== 'active') {
                    return back()->withErrors(['error' => 'Account is not active.']);
                }

                $balanceBefore = $customer->opening_balance;
                $amount = $validated['amount'];
                $balanceAfter = $balanceBefore + $amount;

                // Create transaction
                $transaction = Transaction::create([
                    'customer_id' => $customer->id,
                    'transaction_type' => 'deposit',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'status' => 'approved', // Deposits are immediately approved
                    'reference_number' => $this->generateReferenceNumber(),
                    'created_by' => auth()->id(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update customer balance
                $customer->update(['opening_balance' => $balanceAfter]);

                return redirect()->route('teller.deposit')
                    ->with('success', "Deposit of {$amount} processed successfully. Reference: {$transaction->reference_number}");
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============ Withdrawal ============
    public function withdrawalForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.withdrawal', compact('customers'));
    }

    public function storeWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:10',
            'notes'       => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $customer = Customer::findOrFail($validated['customer_id']);

                // Validate account status
                if ($customer->status !== 'active') {
                    return back()->withErrors(['error' => 'Account is not active.']);
                }

                $balanceBefore = $customer->opening_balance;
                $amount = $validated['amount'];
                $balanceAfter = $balanceBefore - $amount;

                // Check balance rules
                if ($customer->account_type === 'savings') {
                    // Savings account cannot go negative
                    if ($balanceAfter < 0) {
                        return back()->withErrors(['error' => 'Insufficient balance for withdrawal.']);
                    }
                } else {
                    // Current account with overdraft
                    if ($customer->overdraft_enabled && $customer->overdraft_limit) {
                        if ($balanceAfter < -$customer->overdraft_limit) {
                            return back()->withErrors(['error' => 'Overdraft limit exceeded.']);
                        }
                    } else {
                        // No overdraft allowed
                        if ($balanceAfter < 0) {
                            return back()->withErrors(['error' => 'Insufficient balance.']);
                        }
                    }
                }

                // Determine if approval is needed
                $requiresApproval = $amount > $this->approvalLimit;
                $status = $requiresApproval ? 'pending' : 'approved';

                // Create transaction
                $transaction = Transaction::create([
                    'customer_id' => $customer->id,
                    'transaction_type' => 'withdrawal',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'status' => $status,
                    'reference_number' => $this->generateReferenceNumber(),
                    'created_by' => auth()->id(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update balance only if approved
                if ($status === 'approved') {
                    $customer->update(['opening_balance' => $balanceAfter]);
                }

                $message = $requiresApproval 
                    ? "Withdrawal request submitted for approval. Reference: {$transaction->reference_number}"
                    : "Withdrawal of {$amount} processed successfully. Reference: {$transaction->reference_number}";

                return redirect()->route('teller.withdrawal')
                    ->with('success', $message);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============ Transfer ============
    public function transferForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.transfer', compact('customers'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'from_customer_id' => 'required|exists:customers,id',
            'to_customer_id'   => 'required|exists:customers,id|different:from_customer_id',
            'amount'           => 'required|numeric|min:0.01',
            'notes'            => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $fromCustomer = Customer::findOrFail($validated['from_customer_id']);
                $toCustomer = Customer::findOrFail($validated['to_customer_id']);

                // Validate account status
                if ($fromCustomer->status !== 'active' || $toCustomer->status !== 'active') {
                    return back()->withErrors(['error' => 'One or both accounts are not active.']);
                }

                $amount = $validated['amount'];
                $debitReferenceNumber = $this->generateReferenceNumber();
                $creditReferenceNumber = $this->generateReferenceNumber();

                // Check balance for from account
                $fromBalanceBefore = $fromCustomer->opening_balance;
                $fromBalanceAfter = $fromBalanceBefore - $amount;

                if ($fromCustomer->account_type === 'savings') {
                    if ($fromBalanceAfter < 0) {
                        return back()->withErrors(['error' => 'Insufficient balance in source account.']);
                    }
                } else {
                    if ($fromCustomer->overdraft_enabled && $fromCustomer->overdraft_limit) {
                        if ($fromBalanceAfter < -$fromCustomer->overdraft_limit) {
                            return back()->withErrors(['error' => 'Overdraft limit exceeded in source account.']);
                        }
                    } else {
                        if ($fromBalanceAfter < 0) {
                            return back()->withErrors(['error' => 'Insufficient balance in source account.']);
                        }
                    }
                }

                $requiresApproval = $amount > $this->approvalLimit;
                $status = $requiresApproval ? 'pending' : 'approved';

                // Create debit transaction (withdrawal)
                $debitTransaction = Transaction::create([
                    'customer_id' => $fromCustomer->id,
                    'transaction_type' => 'transfer',
                    'amount' => $amount,
                    'balance_before' => $fromBalanceBefore,
                    'balance_after' => $fromBalanceAfter,
                    'status' => $status,
                    'reference_number' => $debitReferenceNumber,
                    'created_by' => auth()->id(),
                    'notes' => "Transfer to {$toCustomer->first_name} {$toCustomer->last_name}. " . ($validated['notes'] ?? ''),
                ]);

                // Create credit transaction (deposit)
                $toBalanceBefore = $toCustomer->opening_balance;
                $toBalanceAfter = $toBalanceBefore + $amount;

                $creditTransaction = Transaction::create([
                    'customer_id' => $toCustomer->id,
                    'transaction_type' => 'transfer',
                    'amount' => $amount,
                    'balance_before' => $toBalanceBefore,
                    'balance_after' => $toBalanceAfter,
                    'status' => $status,
                    'reference_number' => $creditReferenceNumber,
                    'linked_transaction_id' => $debitTransaction->id,
                    'created_by' => auth()->id(),
                    'notes' => "Transfer from {$fromCustomer->first_name} {$fromCustomer->last_name}. " . ($validated['notes'] ?? ''),
                ]);

                // Link transactions
                $debitTransaction->update(['linked_transaction_id' => $creditTransaction->id]);

                // Update balances only if approved
                if ($status === 'approved') {
                    $fromCustomer->update(['opening_balance' => $fromBalanceAfter]);
                    $toCustomer->update(['opening_balance' => $toBalanceAfter]);
                }

                $message = $requiresApproval 
                    ? "Transfer request submitted for approval. Reference: {$debitReferenceNumber}"
                    : "Transfer of {$amount} processed successfully. Reference: {$debitReferenceNumber}";

                return redirect()->route('teller.transfer')
                    ->with('success', $message);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============ Transaction History ============
    public function history()
    {
        $transactions = Transaction::with('customer', 'createdBy', 'approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('dashboard.teller.history', compact('transactions'));
    }

    // ============ Approval (Manager only) ============
    public function pendingApprovals()
    {
        $transactions = Transaction::where('status', 'pending')
            ->with('customer', 'createdBy')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        return view('dashboard.teller.approvals', compact('transactions'));
    }

    public function approveTransaction(Request $request, $transactionId)
    {
        try {
            return DB::transaction(function () use ($request, $transactionId) {
                $transaction = Transaction::findOrFail($transactionId);
                $customer = $transaction->customer;

                if ($transaction->status !== 'pending') {
                    return back()->withErrors(['error' => 'Transaction is not pending.']);
                }

                // Update transaction
                $transaction->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);

                // Update customer balance
                $customer->update(['opening_balance' => $transaction->balance_after]);

                // If transfer, also update linked customer
                if ($transaction->transaction_type === 'transfer' && $transaction->linked_transaction_id) {
                    $linkedTransaction = Transaction::find($transaction->linked_transaction_id);
                    if ($linkedTransaction) {
                        $linkedTransaction->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                        ]);
                        $linkedCustomer = $linkedTransaction->customer;
                        $linkedCustomer->update(['opening_balance' => $linkedTransaction->balance_after]);
                    }
                }

                return back()->with('success', 'Transaction approved successfully.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectTransaction(Request $request, $transactionId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $transaction = Transaction::findOrFail($transactionId);

            if ($transaction->status !== 'pending') {
                return back()->withErrors(['error' => 'Transaction is not pending.']);
            }

            $transaction->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'rejected_reason' => $validated['reason'],
            ]);

            // If transfer, reject linked transaction too
            if ($transaction->transaction_type === 'transfer' && $transaction->linked_transaction_id) {
                $linkedTransaction = Transaction::find($transaction->linked_transaction_id);
                if ($linkedTransaction) {
                    $linkedTransaction->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'rejected_reason' => "Linked transaction rejected: {$validated['reason']}",
                    ]);
                }
            }

            return back()->with('success', 'Transaction rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============ Helper Functions ============
    private function generateReferenceNumber()
    {
        $prefix = 'TXN-' . date('Ymd');
        do {
            $suffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $referenceNumber = $prefix . $suffix;
        } while (Transaction::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }
}
