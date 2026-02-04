<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'transaction_type', // deposit, withdrawal, transfer
        'amount',
        'balance_before',
        'balance_after',
        'status', // pending, approved, rejected
        'reference_number',
        'linked_transaction_id', // for transfers
        'created_by', // teller id
        'approved_by', // manager id
        'rejected_reason',
        'notes',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function linkedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'linked_transaction_id');
    }

    public function relatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'linked_transaction_id');
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
