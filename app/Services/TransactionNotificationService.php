<?php

namespace App\Services;

use App\Mail\TransactionNotification;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

class TransactionNotificationService
{
    public function notify(Transaction $transaction): void
    {
        $transaction->loadMissing('customer');
        $customer = $transaction->customer;

        if (!$customer) {
            return;
        }

        if ($transaction->status !== 'approved') {
            return;
        }

        if ($customer->status !== 'active') {
            return;
        }

        if ($customer->is_frozen) {
            return;
        }

        if (empty($customer->email)) {
            return;
        }

        Mail::to($customer->email)->queue(new TransactionNotification($transaction));
    }
}
