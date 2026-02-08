<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private TransactionService $transactions;

    public function __construct(TransactionService $transactions)
    {
        $this->transactions = $transactions;
    }

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
            $transaction = $this->transactions->deposit($validated);

            return redirect()->route('teller.deposit')
                ->with('success', "Deposit of {$validated['amount']} processed successfully. Reference: {$transaction->reference_number}");
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
            $result = $this->transactions->withdraw($validated);
            $transaction = $result['transaction'];
            $requiresApproval = $result['requiresApproval'];

            $message = $requiresApproval
                ? "Withdrawal request submitted for approval. Reference: {$transaction->reference_number}"
                : "Withdrawal of {$validated['amount']} processed successfully. Reference: {$transaction->reference_number}";

            return redirect()->route('teller.withdrawal')
                ->with('success', $message);
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
            $result = $this->transactions->transfer($validated);
            $transaction = $result['transaction'];
            $requiresApproval = $result['requiresApproval'];

            $message = $requiresApproval
                ? "Transfer request submitted for approval. Reference: {$transaction->reference_number}"
                : "Transfer of {$validated['amount']} processed successfully. Reference: {$transaction->reference_number}";

            return redirect()->route('teller.transfer')
                ->with('success', $message);
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
            $transaction = Transaction::with('customer')->findOrFail($transactionId);
            $this->transactions->approve($transaction, auth()->id());

            return back()->with('success', 'Transaction approved successfully.');
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
            $this->transactions->reject($transaction, auth()->id(), $validated['reason']);

            return back()->with('success', 'Transaction rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
