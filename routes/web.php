<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TellerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ManagerController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('manager')) {
            return redirect()->route('dashboard.manager');
        } elseif ($user->hasRole('staff')) {
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

    // Manager Dashboard
    Route::get('/manager', [ManagerController::class, 'dashboard'])->name('dashboard.manager');

    // Staff Dashboard
    Route::get('/staff', [StaffController::class, 'dashboard'])->name('dashboard.staff');
});

Route::middleware(['auth', 'role:manager|staff'])->prefix('customers')->name('customers.')->controller(StaffController::class)->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/', 'index')->name('index');
        Route::put('/{id}', 'update')->name('update')->whereNumber('id');
        Route::delete('/{id}', 'destroy')->name('destroy')->whereNumber('id');
        Route::get('/deleted', 'deleted')->name('deleted');
        Route::get('/{id}', 'show')->name('show')->whereNumber('id');
    });


Route::middleware(['auth', 'role:manager|staff'])->prefix('customers')->name('customers.')->controller(CustomerDocumentController::class)->group(function () {
    Route::get('/{customer}/documents', 'documents_create')->name('documents.create')->whereNumber('customer');
    Route::post('/{customer}/documents', 'documents_store')->name('documents.store');
    Route::get('/documents/verify', 'customers_verify')->name('verify');
    Route::get('/documents/{document}', 'documents_show')->name('documents.show')->whereNumber('document');
    Route::post('/verify/confirm', 'verify_confirm')->name('verify.confirm');
});

Route::middleware(['auth', 'role:manager|staff'])->controller(CustomerController::class)->group(function () {
    Route::get('/customers-list', 'customersList')->name('customers.customersList');
    Route::get('/customers/{id}/details','customerDetails')->name('customers.customerDetails')->whereNumber('id');
    Route::get('/customers/search', 'searchCustomers')->name('customers.search');
});

Route::middleware(['auth', 'role:staff'])->prefix('teller')->name('teller.')->controller(TellerController::class)->group(function () {
    Route::get('/deposit', 'depositForm')->name('deposit');
    Route::get('/withdrawal', 'withdrawalForm')->name('withdrawal');
    Route::get('/transfer', 'transferForm')->name('transfer');
});

Route::middleware(['auth', 'role:staff'])->prefix('teller')->name('teller.')->controller(TransactionController::class)->group(function () {
    Route::post('/deposit', 'storeDeposit')->name('deposit.store');
    Route::post('/withdrawal', 'storeWithdrawal')->name('withdrawal.store');
    Route::post('/transfer', 'storeTransfer')->name('transfer.store');
    Route::get('/history', 'history')->name('history');
    Route::get('/approvals', 'pendingApprovals')->name('approvals');
    Route::post('/approve/{id}', 'approveTransaction')->name('approve');
    Route::post('/reject/{id}', 'rejectTransaction')->name('reject');
    Route::post('/transfers/{transfer}/approve', 'approveTransfer')->name('transfers.approve');
    Route::post('/transfers/{transfer}/reject', 'rejectTransfer')->name('transfers.reject');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->controller(ManagerController::class)->group(function () {
    Route::get('/approvals/accounts', 'accountApprovals')->name('approvals.accounts');
    Route::post('/approvals/accounts/{customer}/approve', 'approveAccount')->name('approvals.accounts.approve');
    Route::post('/approvals/accounts/{customer}/reject', 'rejectAccount')->name('approvals.accounts.reject');

    Route::get('/approvals/transactions', 'transactionApprovals')->name('approvals.transactions');
    Route::post('/approvals/transactions/{transaction}/approve', 'approveTransaction')->name('approvals.transactions.approve');
    Route::post('/approvals/transactions/{transaction}/reject', 'rejectTransaction')->name('approvals.transactions.reject');
    Route::post('/approvals/transfers/{transfer}/approve', 'approveTransfer')->name('approvals.transfers.approve');
    Route::post('/approvals/transfers/{transfer}/reject', 'rejectTransfer')->name('approvals.transfers.reject');

    Route::get('/monitoring/summary', 'dailySummary')->name('monitoring.summary');
    Route::get('/monitoring/staff', 'staffActivity')->name('monitoring.staff');
    Route::get('/monitoring/audits', 'auditTrail')->name('audits');

    Route::get('/customers', 'customers')->name('customers');
    Route::post('/customers/interest-rate', 'updateSavingsInterestRate')->name('customers.interest-rate');
    Route::get('/customers/{customer}', 'customerShow')->name('customers.show');
    Route::delete('/customers/{customer}/force', 'forceDeleteCustomer')->name('customers.force-delete');
    Route::post('/customers/{customer}/freeze', 'freezeAccount')->name('customers.freeze');
    Route::post('/customers/{customer}/unfreeze', 'unfreezeAccount')->name('customers.unfreeze');

    Route::get('/exceptions', 'exceptions')->name('exceptions');
    Route::post('/exceptions/{transaction}/reverse', 'reverseTransaction')->name('exceptions.reverse');
});


