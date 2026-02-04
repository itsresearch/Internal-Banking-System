<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create()
    {
        return view('dashboard.staff.staff');
    }

    public function index()
    {
        // Placeholder: reuse creation form until a listing page is designed
        return view('dashboard.staff.staff');
    }

    public function show($id)
    {
        $customer = Customer::with('documents')->findOrFail($id);

        return view('dashboard.customer.verify', compact('customer'));
    }

    // Store customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_holder_type' => 'required|in:individual,business',
            'account_type'        => 'required|in:savings,current',
            'opening_balance'     => 'required|numeric|min:0',
            'business_name'       => 'nullable|required_if:account_holder_type,business|string|max:255',
            'business_pan_vat'    => 'nullable|required_if:account_holder_type,business|string|max:100',
            'business_phone'      => 'nullable|required_if:account_holder_type,business|string|max:50',
            'business_email'      => 'nullable|required_if:account_holder_type,business|email|max:150',
            'business_type'       => 'nullable|required_if:account_holder_type,business|in:company,firm,proprietorship,other',
            'registration_number' => 'nullable|required_if:account_holder_type,business|string|max:150',
            'business_address'    => 'nullable|required_if:account_holder_type,business|string|max:255',
            'monthly_withdrawal_limit' => 'nullable|integer|min:0',
            'overdraft_enabled'   => 'nullable|boolean',
            'overdraft_limit'     => 'nullable|required_if:overdraft_enabled,1|numeric|min:0',
            'authorized_signatory'=> 'nullable|required_if:account_type,current|string|max:255',
            'occupation'          => 'nullable|string|max:150',
            'first_name'          => 'required|string|max:100',
            'middle_name'         => 'nullable|string|max:100',
            'last_name'           => 'required|string|max:100',
            'fathers_name'        => 'required|string|max:100',
            'mothers_name'        => 'required|string|max:100',
            'date_of_birth'       => 'required|date',
            'gender'              => 'required|in:male,female,other',
            'phone'               => 'required|string|max:20',
            'email'               => 'required|email|max:100',
            'permanent_address'   => 'required|string|max:255',
            'temporary_address'   => 'required|string|max:255',
            'status'              => 'required|in:active,inactive',
        ]);

        $validated['customer_code'] = $this->generateCustomerCode();
        $validated['interest_rate'] = $this->defaultInterestRate($validated['account_type']);
        $validated['account_opened_at'] = now();

        if ($validated['account_type'] === 'savings') {
            $validated['monthly_withdrawal_limit'] = $validated['monthly_withdrawal_limit'] ?? 4; 
            $validated['overdraft_enabled'] = false;
            $validated['overdraft_limit'] = null;
            $validated['authorized_signatory'] = null;
        } else {
            $validated['monthly_withdrawal_limit'] = null; 
            $validated['overdraft_enabled'] = $request->boolean('overdraft_enabled');
            $validated['overdraft_limit'] = $validated['overdraft_enabled'] ? ($validated['overdraft_limit'] ?? 0.00) : null;
        }

        if ($validated['account_holder_type'] === 'individual') {
            $validated['business_name'] = null;
            $validated['business_pan_vat'] = null;
            $validated['business_phone'] = null;
            $validated['business_email'] = null;
            $validated['business_type'] = null;
            $validated['registration_number'] = null;
            $validated['business_address'] = null;
        }

        $validated['created_by'] = auth()->id();
        $validated['account_number'] = $this->generateAccountNumber();

        $customer = Customer::create($validated);

        return redirect()->route('customers.documents.create', $customer->id);
    }

    // Show document upload form
    public function documents_create($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        return view('dashboard.customer.documents', compact('customer'));
    }

    // Store customer document
    public function documents_store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'citizenship_number' => 'required|string|max:50',
            'citizenship_front'  => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'citizenship_back'   => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'customer_photo'     => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $citizenshipFrontPath = $request->file('citizenship_front')
            ->store('customer_documents', 'public');
        $citizenshipBackPath = $request->file('citizenship_back')
            ->store('customer_documents', 'public');
        $photoPath = $request->file('customer_photo')
            ->store('customer_documents', 'public');

        CustomerDocument::create([
            'customer_id'     => $validated['customer_id'],
            'document_type'   => 'citizenship',
            'document_side'   => 'front',
            'document_number' => $validated['citizenship_number'],
            'file_path'       => $citizenshipFrontPath,
            'uploaded_at'     => now(),
        ]);

        CustomerDocument::create([
            'customer_id'     => $validated['customer_id'],
            'document_type'   => 'citizenship',
            'document_side'   => 'back',
            'document_number' => $validated['citizenship_number'],
            'file_path'       => $citizenshipBackPath,
            'uploaded_at'     => now(),
        ]);

        CustomerDocument::create([
            'customer_id'     => $validated['customer_id'],
            'document_type'   => 'photo',
            'document_side'   => null,
            'document_number' => $validated['citizenship_number'],
            'file_path'       => $photoPath,
            'uploaded_at'     => now(),
        ]);

        return redirect()->route('customers.verify', ['customer_id' => $validated['customer_id']]);
    }

    // Update customer
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'account_holder_type' => 'required|in:individual,business',
            'account_type'        => 'required|in:savings,current',
            'opening_balance'     => 'required|numeric|min:0',
            'business_name'       => 'nullable|required_if:account_holder_type,business|string|max:255',
            'business_pan_vat'    => 'nullable|required_if:account_holder_type,business|string|max:100',
            'business_phone'      => 'nullable|required_if:account_holder_type,business|string|max:50',
            'business_email'      => 'nullable|required_if:account_holder_type,business|email|max:150',
            'business_type'       => 'nullable|required_if:account_holder_type,business|in:company,firm,proprietorship,other',
            'registration_number' => 'nullable|required_if:account_holder_type,business|string|max:150',
            'business_address'    => 'nullable|required_if:account_holder_type,business|string|max:255',
            'monthly_withdrawal_limit' => 'nullable|integer|min:0',
            'overdraft_enabled'   => 'nullable|boolean',
            'overdraft_limit'     => 'nullable|required_if:overdraft_enabled,1|numeric|min:0',
            'authorized_signatory'=> 'nullable|required_if:account_type,current|string|max:255',
            'occupation'          => 'nullable|string|max:150',
            'first_name'          => 'required|string|max:100',
            'middle_name'         => 'nullable|string|max:100',
            'last_name'           => 'required|string|max:100',
            'email'               => 'required|email|max:100|unique:customers,email,' . $id,
            'phone'               => 'required|string|max:20',
            'date_of_birth'       => 'required|date',
            'gender'              => 'required|in:male,female,other',
            'permanent_address'   => 'required|string|max:255',
            'temporary_address'   => 'required|string|max:255',
            'status'              => 'required|in:active,inactive',
        ]);

        if ($validated['account_type'] === 'savings') {
            $validated['monthly_withdrawal_limit'] = $validated['monthly_withdrawal_limit'] ?? ($customer->monthly_withdrawal_limit ?? 4);
            $validated['overdraft_enabled'] = false;
            $validated['overdraft_limit'] = null;
            $validated['authorized_signatory'] = null;
        } else {
            $validated['monthly_withdrawal_limit'] = null;
            $validated['overdraft_enabled'] = $request->boolean('overdraft_enabled');
            $validated['overdraft_limit'] = $validated['overdraft_enabled'] ? ($validated['overdraft_limit'] ?? $customer->overdraft_limit ?? 0.00) : null;
        }

        if ($validated['account_holder_type'] === 'individual') {
            $validated['business_name'] = null;
            $validated['business_pan_vat'] = null;
            $validated['business_phone'] = null;
            $validated['business_email'] = null;
            $validated['business_type'] = null;
            $validated['registration_number'] = null;
            $validated['business_address'] = null;
        }

        // Preserve manager-set interest rate if already present; otherwise set default
        $validated['interest_rate'] = $customer->interest_rate ?? $this->defaultInterestRate($validated['account_type']);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Customer updated successfully.');
    }

    public function customers_verify(Request $request)
    {
        $customerId = $request->input('customer_id');
        $customer = Customer::with('documents')->findOrFail($customerId);

        return view('dashboard.customer.verify', compact('customer'));
    }

    public function verify_confirm(Request $request)
    {
        $customerId = $request->input('customer_id');
        $customer = Customer::findOrFail($customerId);

        // Assuming verification sets status to 'verified' or something
        // For now, just redirect with success message
        // You can add logic to update status or send email, etc.

        return redirect()->route('customers.index')->with('success', 'Customer verified successfully.');
    }

    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = (string) random_int(10 ** 15, (10 ** 16) - 1);
        } while (Customer::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    private function generateCustomerCode(): string
    {
        do {
            $code = 'CUST-' . strtoupper(str_pad(dechex(random_int(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT));
        } while (Customer::where('customer_code', $code)->exists());

        return $code;
    }

    private function defaultInterestRate(string $accountType): float
    {
        // Demo defaults; manager-managed rates can be wired later
        return $accountType === 'savings' ? 5.00 : 0.00;
    }
}

