<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Transaction;

class CustomerController extends Controller
{
    //
    public function customersList()
    {
        $q = trim((string) request('q', ''));

        $customersQuery = DB::table('customers');

        if ($q !== '') {
            // Search by name, account number, email, phone, and citizenship number (stored as document_number)
            $customersQuery->where(function ($sub) use ($q) {
                $sub
                    ->where('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$q}%"])
                    ->orWhere('account_number', 'like', "%{$q}%")
                    ->orWhere('customer_code', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereExists(function ($doc) use ($q) {
                        $doc->select(DB::raw(1))
                            ->from('customer_documents')
                            ->whereColumn('customer_documents.customer_id', 'customers.id')
                            ->where('customer_documents.document_type', 'citizenship')
                            ->where('customer_documents.document_number', 'like', "%{$q}%");
                    });
            });
        }

        $customers = $customersQuery
            ->orderBy('id', 'desc')
            ->get();

        return view('dashboard.customer.all_customers', compact('customers'));
    }
    public function customerDetails($id)
    {
        $customer = DB::table('customers')->find($id);
        $documents = DB::table('customer_documents')->where('customer_id', $id)->get();
        $photo = $documents->where('document_type', 'photo')->first();
        $citizenship_front = $documents->where('document_type', 'citizenship')->where('document_side', 'front')->first();
        $citizenship_back = $documents->where('document_type', 'citizenship')->where('document_side', 'back')->first();

        $transactions = Transaction::with(['createdBy', 'approvedBy'])
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Current balance is stored on customers.opening_balance in this system.
        $currentBalance = $customer?->opening_balance ?? 0;

        return view('dashboard.customer.customer_details', compact(
            'customer',
            'photo',
            'citizenship_front',
            'citizenship_back',
            'transactions',
            'currentBalance'
        ));
    }

    /**
     * Lightweight customer search for transaction forms.
     * Query by name, account number, or citizenship number.
     */
    public function searchCustomers(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $customers = DB::table('customers')
            ->select('id', 'first_name', 'last_name', 'account_number', 'account_type', 'opening_balance', 'overdraft_enabled', 'overdraft_limit', 'status')
            ->where(function ($sub) use ($q) {
                $sub
                    ->where('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$q}%"])
                    ->orWhere('account_number', 'like', "%{$q}%")
                    ->orWhereExists(function ($doc) use ($q) {
                        $doc->select(DB::raw(1))
                            ->from('customer_documents')
                            ->whereColumn('customer_documents.customer_id', 'customers.id')
                            ->where('customer_documents.document_type', 'citizenship')
                            ->where('customer_documents.document_number', 'like', "%{$q}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Normalize
        $payload = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')),
                'account_number' => $c->account_number,
                'status' => $c->status,
                'account_type' => $c->account_type,
                'opening_balance' => (float) ($c->opening_balance ?? 0),
                'overdraft_enabled' => (bool) ($c->overdraft_enabled ?? false),
                'overdraft_limit' => (float) ($c->overdraft_limit ?? 0),
            ];
        });

        return response()->json($payload);
    }
}
