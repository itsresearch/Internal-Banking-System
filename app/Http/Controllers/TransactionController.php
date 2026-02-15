<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transfer;
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

    // ============ Deposit =============
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

            return redirect()->route('teller.withdrawal')
                ->with('success', "Withdrawal of {$validated['amount']} processed successfully. Reference: {$transaction->reference_number}");
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
            $transfer = $result['transfer'];
            $requiresApproval = $result['requiresApproval'];

            $message = $requiresApproval
                ? "Transfer request submitted for approval. Reference: {$transfer->reference_number}"
                : "Transfer of {$validated['amount']} processed successfully. Reference: {$transfer->reference_number}";

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
        $transfers = Transfer::with(['fromCustomer', 'toCustomer', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'transfers');

        return view('dashboard.teller.history', compact('transactions', 'transfers'));
    }

    // ============ Approval (Manager only) ============
    public function pendingApprovals()
    {
        $transactions = Transaction::where('status', 'pending')
            ->with('customer', 'createdBy')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        $transfers = Transfer::where('status', 'pending')
            ->with(['fromCustomer', 'toCustomer', 'createdBy'])
            ->orderBy('created_at', 'asc')
            ->paginate(20, ['*'], 'transfers');

        return view('dashboard.teller.approvals', compact('transactions', 'transfers'));
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

    public function approveTransfer(Request $request, $transferId)
    {
        try {
            $transfer = Transfer::with(['fromCustomer', 'toCustomer'])->findOrFail($transferId);
            $this->transactions->approveTransfer($transfer, auth()->id());

            return back()->with('success', 'Transfer approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectTransfer(Request $request, $transferId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $transfer = Transfer::findOrFail($transferId);
            $this->transactions->rejectTransfer($transfer, auth()->id(), $validated['reason']);

            return back()->with('success', 'Transfer rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
