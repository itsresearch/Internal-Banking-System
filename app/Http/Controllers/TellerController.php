<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class TellerController extends Controller
{
    public function depositForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.deposit', compact('customers'));
    }

    public function withdrawalForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.withdrawal', compact('customers'));
    }

    public function transferForm()
    {
        $customers = Customer::where('status', 'active')->get();
        return view('dashboard.teller.transfer', compact('customers'));
    }
}
