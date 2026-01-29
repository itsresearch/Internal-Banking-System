<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $usertype = $user->usertype;

        if ($usertype === 'admin') {
            return redirect()->route('dashboard.admin');
        } elseif ($usertype === 'manager') {
            return redirect()->route('dashboard.manager');
        } elseif ($usertype === 'staff') {
            return redirect()->route('dashboard.staff');
        }
    }
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Admin Dashboard
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    Route::get('/admin', function () {
        return view('dashboard.admin.admin');
    })->name('dashboard.admin');

    // Manager Dashboard
    Route::get('/manager', function () {
        return view('dashboard.manager.manager');
    })->name('dashboard.manager');

    // Staff Dashboard
    Route::get('/staff', function () {
        return view('dashboard.staff.staff');
    })->name('dashboard.staff');
});

Route::fallback(function () {
    return "<h1>Page not found.</h1>";
});

Route::middleware(['auth'])->group(function () {
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::post('/customers_documents', [CustomerController::class, 'documents_store'])->name('customers.documents');
    Route::get('/customers/{id}/documents', [CustomerController::class, 'documents_create'])
        ->name('customers.documents.create');

    Route::post('/customers/documents', [CustomerController::class, 'documents_store'])
        ->name('customers.documents.store');
});
