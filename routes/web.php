<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StaffController;


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

Route::middleware(['auth'])->controller(StaffController::class)->group(function () {
    Route::get('/customers/create', 'create')->name('customers.create');
    Route::post('/customers', 'store')->name('customers.store');
    Route::get('/customers/{id}', 'show')->name('customers.show');
    Route::get('/customers', 'index')->name('customers.index');
    Route::put('/customers/{id}', 'update')->name('customers.update');
    Route::get('/customers/{id}/documents', 'documents_create')->name('customers.documents.create');
    Route::post('/customers/documents', 'documents_store')->name('customers.documents.store');
    Route::get('/customers/documents/verify', 'customers_verify')->name('customers.verify');
    Route::post('/customers/verify/confirm', 'verify_confirm')->name('customers.verify.confirm');
});

Route::middleware(['auth'])->controller(CustomerController::class)->group(function () {
    Route::get('/customers-list', 'customersList')->name('customers.customersList');
});
