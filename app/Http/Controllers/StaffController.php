<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function dashboard()
    {
        $staffId = auth()->id();

        $customersToday = Customer::where('created_by', $staffId)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $customersTotal = Customer::where('created_by', $staffId)->count();

        $pendingBusinessAccounts = Customer::where('created_by', $staffId)
            ->where('status', 'pending')
            ->count();

        $softDeletedByYou = Customer::onlyTrashed()
            ->where('created_by', $staffId)
            ->count();

        $recentCustomers = Customer::where('created_by', $staffId)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('dashboard.staff.staff', compact(
            'customersToday',
            'customersTotal',
            'pendingBusinessAccounts',
            'softDeletedByYou',
            'recentCustomers'
        ));
    }

    public function create()
    {
        return view('dashboard.staff.customers_create');
    }

    public function index()
    {
        return redirect()->route('customers.customersList');
    }

    public function deleted()
    {
        $customers = Customer::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('dashboard.customer.deleted_customers', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with(['documents', 'businessAccount'])->findOrFail($id);

        return view('dashboard.customer.verify', compact('customer'));
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return back()->with('success', 'Customer moved to deleted list.');
    }

    // Store customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_holder_type' => 'required|in:individual,business',
            'account_type'        => 'required|in:savings,current',
            'balance'             => 'required|numeric|min:0',
            'business_name'       => 'required|required_if:account_holder_type,business|string|max:255',
            'business_pan_vat'    => 'required|required_if:account_holder_type,business|string|max:100',
            'business_phone'      => 'required|required_if:account_holder_type,business|string|max:50',
            'business_email'      => 'required|required_if:account_holder_type,business|email|max:150',
            'business_type'       => 'required|required_if:account_holder_type,business|in:company,firm,proprietorship,other',
            'registration_number' => 'required|required_if:account_holder_type,business|string|max:150',
            'business_address'    => 'required|required_if:account_holder_type,business|string|max:255',
            'authorized_signatory'=> 'required|required_if:account_holder_type,business|string|max:255',
            'occupation'          => 'required|string|max:150',
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
        ]);

        $validated['customer_code'] = $this->generateCustomerCode();
        $validated['interest_rate'] = $this->defaultInterestRate($validated['account_type']);
        $validated['account_opened_at'] = now();

        if ($validated['account_holder_type'] === 'individual') {
            $validated['business_name'] = null;
            $validated['business_pan_vat'] = null;
            $validated['business_phone'] = null;
            $validated['business_email'] = null;
            $validated['business_type'] = null;
            $validated['registration_number'] = null;
            $validated['business_address'] = null;
            $validated['authorized_signatory'] = null;
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['account_holder_type'] === 'business' ? 'pending' : 'active';  //ternary operator to set status based on account holder type ..... condition ? value_if_true : value_if_false

        $validated['account_number'] = $this->generateAccountNumber();

        $customer = Customer::create([
            'customer_code' => $validated['customer_code'],
            'account_number' => $validated['account_number'],
            'account_type' => $validated['account_type'],
            'account_holder_type' => $validated['account_holder_type'],
            'interest_rate' => $validated['interest_rate'],
            'balance' => $validated['balance'],
            'minimum_balance' => 0,
            'account_opened_at' => $validated['account_opened_at'],
            'occupation' => $validated['occupation'] ?? null,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'fathers_name' => $validated['fathers_name'],
            'mothers_name' => $validated['mothers_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'permanent_address' => $validated['permanent_address'],
            'temporary_address' => $validated['temporary_address'],
            'status' => $validated['status'],
            'created_by' => $validated['created_by'],
        ]);

        if ($validated['account_holder_type'] === 'business') {
            $customer->businessAccount()->create([
                'business_name' => $validated['business_name'],
                'business_pan_vat' => $validated['business_pan_vat'],
                'business_phone' => $validated['business_phone'],
                'business_email' => $validated['business_email'],
                'business_type' => $validated['business_type'],
                'registration_number' => $validated['registration_number'],
                'business_address' => $validated['business_address'],
                'authorized_signatory' => $validated['authorized_signatory'],
            ]);
        }

        return redirect()->route('customers.documents.create', $customer->id);
    }


    // Update customer
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'email'               => 'required|email|max:100|unique:customers,email,' . $id,
            'phone'               => 'required|string|max:10',
            'permanent_address'   => 'required|string|max:255',
            'temporary_address'   => 'required|string|max:255',
            'citizenship_number'  => 'required|string|max:10',
            'citizenship_front'   => 'required|file|mimes:jpg,jpeg,png',
            'citizenship_back'    => 'required|file|mimes:jpg,jpeg,png',
            'customer_photo'      => 'required|file|mimes:jpg,jpeg,png',
        ]);

        $customer->update([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'permanent_address' => $validated['permanent_address'],
            'temporary_address' => $validated['temporary_address'],
        ]);

        $citizenshipNumber = $validated['citizenship_number'];

        if ($request->hasFile('citizenship_front')) {
            $path = $request->file('citizenship_front')->store('customer_documents', 'public');
            CustomerDocument::updateOrCreate(
                ['customer_id' => $customer->id, 'document_type' => 'citizenship', 'document_side' => 'front'],
                ['document_number' => $citizenshipNumber, 'file_path' => $path, 'uploaded_at' => now()]
            );
        }

        if ($request->hasFile('citizenship_back')) {
            $path = $request->file('citizenship_back')->store('customer_documents', 'public');
            CustomerDocument::updateOrCreate(
                ['customer_id' => $customer->id, 'document_type' => 'citizenship', 'document_side' => 'back'],
                ['document_number' => $citizenshipNumber, 'file_path' => $path, 'uploaded_at' => now()]
            );
        }

        if ($request->hasFile('customer_photo')) {
            $path = $request->file('customer_photo')->store('customer_documents', 'public');
            CustomerDocument::updateOrCreate(
                ['customer_id' => $customer->id, 'document_type' => 'photo', 'document_side' => null],
                ['document_number' => $citizenshipNumber, 'file_path' => $path, 'uploaded_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Customer updated successfully.');
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
        if ($accountType !== 'savings') {
            return 0.00;
        }

        $rate = Customer::where('account_type', 'savings')
            ->orderBy('updated_at', 'desc')
            ->value('interest_rate');

        return $rate !== null ? (float) $rate : 5.00;      //biassed.........................................................................
    }


    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = (string) random_int(10 ** 15, (10 ** 16) - 1);
        } while (Customer::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }




}

