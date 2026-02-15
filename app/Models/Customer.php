<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Customer extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable;
    protected $fillable = [
        'customer_code',
        'account_number',
        'account_type',
        'account_holder_type',
        'interest_rate',
        'balance',
        'minimum_balance',
        'account_opened_at',
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
        'approved_by',
        'approved_at',
        'rejected_reason',
        'is_frozen',
        'frozen_at',
        'frozen_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'frozen_at' => 'datetime',
        'is_frozen' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function businessAccount()
    {
        return $this->hasOne(BusinessAccount::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeSearch($query, $q)
    {
        if ($q === '') return $query;

        return $query->where(function ($sub) use ($q) {
            $sub->where('first_name', 'like', "%{$q}%")
                ->orWhere('middle_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$q}%"])
                ->orWhere('account_number', 'like', "%{$q}%")
                ->orWhere('customer_code', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhereHas('documents', function ($doc) use ($q) {
                    $doc->where('document_type', 'citizenship')
                        ->where('document_number', 'like', "%{$q}%");
                });
        });
    }
}
