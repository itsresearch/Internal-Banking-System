<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function customersList()
    {
        $customers = DB::table('customers')->get();
        return view('dashboard.customer.all_customers', compact('customers'));
        // return $customers; 
    }
}
