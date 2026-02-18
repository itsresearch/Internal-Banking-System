<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerDocumentController extends Controller
{
    // Show document upload form
    public function documents_create(Customer $customer)
    {

        return view('dashboard.customer.documents', compact('customer'));
    }

    // Store customer document
    public function documents_store(Request $request, Customer $customer){

    $validated = $request->validate([
        'citizenship_number' => 'required|string|max:50',
        'citizenship_front'  => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'citizenship_back'   => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'customer_photo'     => 'required|image|max:2048',
    ]);

    $folder = "customer_documents/{$customer->id}";

    $citizenshipFrontPath = $request->file('citizenship_front')->store($folder, 'local');
    $citizenshipBackPath = $request->file('citizenship_back')->store($folder, 'local');
    $photoPath = $request->file('customer_photo')->store($folder, 'local');

    $documents = [
        ['type' => 'citizenship', 'side' => 'front', 'file' => $citizenshipFrontPath],
        ['type' => 'citizenship', 'side' => 'back', 'file' => $citizenshipBackPath],
        ['type' => 'photo', 'side' => null, 'file' => $photoPath],
    ];

    foreach ($documents as $doc) {
        CustomerDocument::create([
            'customer_id'     => $customer->id,
            'document_type'   => $doc['type'],
            'document_side'   => $doc['side'],
            'document_number' => $validated['citizenship_number'],
            'file_path'       => $doc['file'],
            'uploaded_at'     => now(),
        ]);
    }
    

    return redirect()
        ->route('customers.show', $customer->id)
        ->with('success', 'Documents uploaded successfully. Please verify the customer details below.');
}


    public function customers_verify(Request $request)
    {
        $customerId = $request->input('customer_id');
        $customer = Customer::with('documents')->findOrFail($customerId);

        return view('dashboard.customer.verify', compact('customer'));
    }

    public function documents_show(CustomerDocument $document)
    {
        $path = $document->file_path;

        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            $disk = Storage::disk('public');
            if (! $disk->exists($path)) {
                abort(404);
            }
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'private, no-store, must-revalidate',
        ]);
    }

    public function verify_confirm(Request $request)
    {
        $customerId = $request->get('customer_id');
        $customer = Customer::findOrFail($customerId);
        return redirect()->route('customers.index')->with('success', 'Customer verified successfully.');
    }
}
