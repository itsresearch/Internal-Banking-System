<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
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
