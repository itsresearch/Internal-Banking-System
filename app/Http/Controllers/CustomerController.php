<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    //
    public function customersList(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->search($q);
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('dashboard.customer.all_customers', compact('customers', 'q'));
    }
    public function customerDetails($id)
    {
        $customer = Customer::find($id);

        $documents = $customer ? $customer->documents() ->where(function ($doc) {
                    $doc->where('document_type', 'photo')
                        ->orWhere(function ($citizenship) {   //closure function
                            $citizenship->where('document_type', 'citizenship') ->whereIn('document_side', ['front', 'back']);
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

        // Current balance is stored on customers.balance in this system.
        $currentBalance = $customer?->balance ?? 0;  // Use null safe operator to avoid errors if customer is null

        return view('dashboard.customer.customer_details', compact(
            'customer',
            'photo',
            'citizenship_front',
            'citizenship_back',
            'transactions',
            'currentBalance'
        ));
    }

    public function searchCustomers(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $customers = Customer::search($q)   //this is scope search in customer model
            ->where('status', 'active')
            ->where('is_frozen', false)
            ->select('id', 'first_name', 'last_name', 'account_number', 'account_type', 'balance', 'status')
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
                'balance' => (float) ($c->balance ?? 0),
            ];
        });

        return response()->json($payload);
    }
}

