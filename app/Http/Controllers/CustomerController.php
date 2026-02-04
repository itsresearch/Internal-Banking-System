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
        $customers = DB::table('customers')->get();
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
}
