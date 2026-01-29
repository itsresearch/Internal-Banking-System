<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create()
    {
        return view('customers.create');
    }

    // Store customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code'       => 'required|unique:customers,customer_code',
            'first_name'          => 'required|string|max:100',
            'middle_name'         => 'nullable|string|max:100',
            'last_name'           => 'required|string|max:100',
            'fathers_name'        => 'nullable|string|max:100',
            'mothers_name'        => 'nullable|string|max:100',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'phone'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:100',
            'permanent_address'   => 'required|string|max:255',
            'temporary_address'   => 'nullable|string|max:255',
            'status'              => 'required|in:active,inactive',
        ]);

        $validated['created_by'] = auth()->id();

        // Save customer
        $customer = Customer::create($validated);

        // Redirect to document upload page
        return redirect()->route('customers.documents.create', $customer->id)
            ->with('success', 'Customer created successfully. Upload documents.');
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
            'customer_id'      => 'required|exists:customers,id',
            'document_type'    => 'required|in:citizenship,passport,photo',
            'document_number'  => 'required|string|max:50',
            'document_file'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = $request->file('document_file')
            ->store('customer_documents', 'public');

        CustomerDocument::create([
            'customer_id'     => $validated['customer_id'],
            'document_type'   => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'file_path'       => $filePath,
            'uploaded_at'     => now(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
}
