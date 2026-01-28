<?php

use Illuminate\Support\Facades\Route;

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
    // Show login page for unauthenticated users
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

