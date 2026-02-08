<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    //
    public function customersList()
    {
        $q = trim((string) request('q', ''));

        $customersQuery = Customer::query();

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
                    ->orWhereHas('documents', function ($doc) use ($q) {
                        $doc->where('document_type', 'citizenship')
                            ->where('document_number', 'like', "%{$q}%");
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
        $customer = Customer::find($id);

        $documents = $customer
            ? $customer->documents()
                ->where(function ($doc) {
                    $doc->where('document_type', 'photo')
                        ->orWhere(function ($citizenship) {
                            $citizenship->where('document_type', 'citizenship')
                                ->whereIn('document_side', ['front', 'back']);
                        });
                })
                ->get()
            : collect();

        $photo = $documents->firstWhere('document_type', 'photo');
        $citizenship_front = $documents
            ->where('document_type', 'citizenship')
            ->where('document_side', 'front')
            ->first();
        $citizenship_back = $documents
            ->where('document_type', 'citizenship')
            ->where('document_side', 'back')
            ->first();

        $transactions = $customer
            ? $customer->transactions()
                ->with(['createdBy', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
            : collect();

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

        $customers = Customer::query()
            ->select('id', 'first_name', 'last_name', 'account_number', 'account_type', 'opening_balance', 'overdraft_enabled', 'overdraft_limit', 'status')
            ->where(function ($sub) use ($q) {
                $sub
                    ->where('first_name', 'like', "%{$q}%")
                    ->orWhere('middle_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$q}%"])
                    ->orWhere('account_number', 'like', "%{$q}%")
                    ->orWhereHas('documents', function ($doc) use ($q) {
                        $doc->where('document_type', 'citizenship')
                            ->where('document_number', 'like', "%{$q}%");
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

