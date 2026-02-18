<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ManagerService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Role;

class ManagerController extends Controller
{
    private ManagerService $managerService;   //from services folder 
    private TransactionService $transactionService;

    public function __construct(ManagerService $managerService, TransactionService $transactionService)  //dependency injection
    {
        $this->managerService = $managerService;
        $this->transactionService = $transactionService;
    }

    public function dashboard()
    {
        $pendingAccounts = Customer::where('status', 'pending')
            ->where('account_holder_type', 'business')
            ->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $frozenAccounts = Customer::where('is_frozen', true)->count();

        return view('dashboard.manager.manager', compact(
            'pendingAccounts',
            'pendingTransactions',
            'frozenAccounts'
        ));
    }

    // ============ Account Approval ============
    public function accountApprovals()
    {
        $accounts = Customer::where('status', 'pending')
            ->where('account_holder_type', 'business')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('dashboard.manager.approvals_accounts', compact('accounts'));
    }

    public function approveAccount(Customer $customer)   // Route Model Binding  alternate to findOrFail..should be customer on route parameter
    {
        // if ($customer->account_holder_type !== 'business') {
        //     return back()->withErrors(['error' => 'Only business accounts require manager approval.']);
        // }
        // if ($customer->status !== 'pending') {
        //     return back()->withErrors(['error' => 'Account is not pending approval.']);
        // }

        $customer->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_reason' => null,
        ]);

        return back()->with('success', 'Account approved successfully.');
    }

    public function rejectAccount(Request $request, Customer $customer)
    {
        // if ($customer->account_holder_type !== 'business') {
        //     return back()->withErrors(['error' => 'Only business accounts require manager approval.']);
        // }
        $validated = $request->validate([
            'reason' => 'required|string|max:100',
        ]);

        $customer->update([
            'status' => 'inactive',
            // 'approved_by' => auth()->id(),
            // 'approved_at' => now(),
            'rejected_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Account rejected.');
    }

    // ============ Transaction Approval ============
    public function transactionApprovals(Request $request)
    {
        $type = $request->query('type');  //query from url like ?type=deposit or ?type=withdrawal or ?type=transfer

        $transactions = Transaction::with(['customer', 'createdBy'])
            ->where('status', 'pending')
            ->when($type && $type !== 'transfer', function ($query) use ($type) {
                $query->where('transaction_type', $type);
            })
            ->when($type === 'transfer', function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(20)
            ->appends($request->query());

        $transfers = Transfer::with(['fromCustomer', 'toCustomer', 'createdBy'])
            ->where('status', 'pending')
            ->when($type && $type !== 'transfer', function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(20, ['*'], 'transfers')
            ->appends($request->query());

        return view('dashboard.manager.approvals_transactions', compact('transactions', 'transfers', 'type'));
    }

    public function approveTransaction(Transaction $transaction)
    {
        try {
            $this->transactionService->approve($transaction, auth()->id());
            return back()->with('success', 'Transaction approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectTransaction(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:100',
        ]);

        try {
            $this->transactionService->reject($transaction, auth()->id(), $validated['reason']);
            return back()->with('success', 'Transaction rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approveTransfer(Transfer $transfer)
    {
        try {
            $this->transactionService->approveTransfer($transfer, auth()->id());
            return back()->with('success', 'Transfer approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectTransfer(Request $request, Transfer $transfer)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->transactionService->rejectTransfer($transfer, auth()->id(), $validated['reason']);
            return back()->with('success', 'Transfer rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============ Monitoring & Review ============
    public function dailySummary(Request $request)
    {
        $fromInput = $request->query('from');
        $toInput = $request->query('to');

        $from = $fromInput ? Carbon::parse($fromInput) : now()->subDays(6);
        $to = $toInput ? Carbon::parse($toInput) : now();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $summary = $this->managerService->dailySummary($from, $to);

        return view('dashboard.manager.monitoring_summary', compact('summary', 'from', 'to'));
    }

    public function staffActivity(Request $request)
    {
        $staffId = $request->query('staff_id');
        $from = $request->query('from');
        $to = $request->query('to');

        $staff = User::role('staff')->orderBy('name')->get();

        $transactions = Transaction::with(['customer', 'createdBy'])
            ->when($staffId, function ($query) use ($staffId) {
                $query->where('created_by', $staffId);
            })
            ->when($from, function ($query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('dashboard.manager.monitoring_staff', compact('transactions', 'staff', 'staffId', 'from', 'to'));
    }

    public function auditTrail(Request $request)
    {
        $userId = $request->query('user_id');
        $event = $request->query('event');
        $model = $request->query('model');
        $from = $request->query('from');
        $to = $request->query('to');

        $users = User::orderBy('name')->get();
        $modelOptions = [
            User::class,
            Customer::class,
            Transaction::class,
            Role::class,
        ];

        $audits = Audit::with('user')
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when($event, function ($query) use ($event) {
                $query->where('event', $event);
            })
            ->when($model, function ($query) use ($model) {
                $query->where('auditable_type', $model);
            })
            ->when($from, function ($query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('dashboard.manager.audit_history', compact('audits','users','modelOptions','userId','event','model','from','to'));
    }

    // ============ Customer & Account Oversight ============
    public function customers(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $deleted = $request->boolean('deleted');

        $currentSavingsRate = Customer::where('account_type', 'savings')
            ->orderBy('updated_at', 'desc')
            ->value('interest_rate');

        if ($currentSavingsRate === null) {
            $currentSavingsRate = 5.00;
        }

        $customersQuery = Customer::query();
        if ($deleted) {
            $customersQuery = $customersQuery->onlyTrashed();
        }

        $customers = $customersQuery
            ->search($q)
            ->when(!$deleted && $status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('dashboard.manager.customers_index', compact(
            'customers',
            'q',
            'status',
            'deleted',
            'currentSavingsRate'
        ));
    }

    public function updateSavingsInterestRate(Request $request)
    {
        $validated = $request->validate([
            'interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        $rate = round((float) $validated['interest_rate'], 2);

        Customer::where('account_type', 'savings')
            ->update(['interest_rate' => $rate]);

        return back()->with('success', "Savings interest rate updated to {$rate}%. ");
    }

    public function customerShow($customerId)
    {
        $customer = Customer::withTrashed()->with('businessAccount')->findOrFail($customerId);
        $documents = $customer->documents()
            ->where(function ($doc) {
                $doc->where('document_type', 'photo')
                    ->orWhere(function ($citizenship) {
                        $citizenship->where('document_type', 'citizenship')
                            ->whereIn('document_side', ['front', 'back']);
                    });
            })
            ->get();

        $photo = $documents->firstWhere('document_type', 'photo');
        $citizenshipFront = $documents
            ->where('document_type', 'citizenship')
            ->where('document_side', 'front')
            ->first();
        $citizenshipBack = $documents
            ->where('document_type', 'citizenship')
            ->where('document_side', 'back')
            ->first();

        $transactions = $customer->transactions()
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('dashboard.manager.customers_show', compact(
            'customer',
            'photo',
            'citizenshipFront',
            'citizenshipBack',
            'transactions'
        ));
    }

    public function forceDeleteCustomer($customerId)
    {
        $customer = Customer::withTrashed()->findOrFail($customerId);

        if (!$customer->trashed()) {
            return back()->withErrors(['error' => 'Customer must be soft deleted first.']);
        }

        $customer->forceDelete();

        return back()->with('success', 'Customer permanently deleted.');
    }

    public function freezeAccount(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $customer->update([
            'is_frozen' => true,
            'frozen_at' => now(),
            'frozen_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Account frozen.');
    }

    public function unfreezeAccount(Customer $customer)
    {
        $customer->update([
            'is_frozen' => false,
            'frozen_at' => null,
            'frozen_reason' => null,
        ]);

        return back()->with('success', 'Account unfrozen.');
    }

    // ============ Exception Handling ============
    public function exceptions()
    {
        $recentApproved = Transaction::with(['customer', 'createdBy'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.manager.exceptions', compact('recentApproved'));
    }

    public function reverseTransaction(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->managerService->reverseTransaction($transaction, auth()->id(), $validated['reason']);
            return back()->with('success', 'Reversal entry created.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
