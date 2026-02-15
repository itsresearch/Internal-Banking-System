<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAccount extends Model
{
    protected $fillable = [
        'customer_id',
        'business_name',
        'business_pan_vat',
        'business_phone',
        'business_email',
        'business_type',
        'registration_number',
        'business_address',
        'authorized_signatory',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
