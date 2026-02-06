<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\Request;

class CustomerDocumentController extends Controller
{
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

        return redirect()
            ->route('customers.show', $validated['customer_id'])
            ->with('success', 'Documents uploaded successfully. Please verify the customer details below.');
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
}
