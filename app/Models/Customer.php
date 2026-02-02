<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'account_number',
        'account_type',
        'account_holder_type',
        'business_name',
        'business_pan_vat',
        'business_phone',
        'business_email',
        'business_type',
        'registration_number',
        'business_address',
        'interest_rate',
        'monthly_withdrawal_limit',
        'overdraft_limit',
        'overdraft_enabled',
        'opening_balance',
        'minimum_balance',
        'account_opened_at',
        'authorized_signatory',
        'nominee_name',
        'nominee_relation',
        'occupation',
        'first_name',
        'middle_name',
        'last_name',
        'fathers_name',
        'mothers_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'permanent_address',
        'temporary_address',
        'status',
        'created_by',
    ];

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }
}
