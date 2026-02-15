<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Transaction extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'customer_id',
        'transaction_type', // deposit, withdrawal, transfer
        'amount',
        'balance_before',
        'balance_after',
        'status', // pending, approved, rejected
        'reference_number',
        'linked_transaction_id', // for transfers
        'reversal_of',
        'is_reversal',
        'reversal_reason',
        'is_adjustment',
        'adjustment_reason',
        'exception_status',
        'exception_reason',
        'created_by', // teller id
        'approved_by', // manager id
        'rejected_reason',
        'notes',
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'is_reversal' => 'boolean',
        'is_adjustment' => 'boolean',
    ];

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

    public function reversalOf()
    {
        return $this->belongsTo(Transaction::class, 'reversal_of');
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
