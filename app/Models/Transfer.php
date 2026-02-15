<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'from_customer_id',
        'to_customer_id',
        'amount',
        'from_balance_before',
        'from_balance_after',
        'to_balance_before',
        'to_balance_after',
        'status',
        'reference_number',
        'created_by',
        'approved_by',
        'rejected_reason',
        'notes',
    ];

    public function fromCustomer()
    {
        return $this->belongsTo(Customer::class, 'from_customer_id');
    }

    public function toCustomer()
    {
        return $this->belongsTo(Customer::class, 'to_customer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
