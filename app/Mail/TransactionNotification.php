<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Mail\Mailable;

class TransactionNotification extends Mailable
{
    public Transaction $transaction;
    public string $customerName;
    public string $maskedAccount;
    public string $actionText;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction->loadMissing('customer');
        $customer = $this->transaction->customer;

        $this->customerName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
        $accountNumber = (string) ($customer->account_number ?? '');
        $this->maskedAccount = $this->maskAccount($accountNumber);
        $this->actionText = $this->resolveActionText($this->transaction);
    }

    public function build()
    {
        return $this->subject('Transaction Notification')
            ->view('emails.transaction_notification');
    }

    private function maskAccount(string $accountNumber): string
    {
        $digits = preg_replace('/\D+/', '', $accountNumber);
        if (strlen($digits) <= 4) {
            return '****' . $digits;
        }

        return '****' . substr($digits, -4);
    }

    private function resolveActionText(Transaction $transaction): string
    {
        $type = $transaction->transaction_type;
        if ($type === 'deposit') {
            return 'deposited to';
        }
        if ($type === 'withdrawal') {
            return 'withdrawn from';
        }
        if ($type === 'transfer') {
            if ($transaction->balance_after >= $transaction->balance_before) {
                return 'transferred to';
            }
            return 'transferred from';
        }

        return 'processed on';
    }
}
