<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'document_type',
        'document_side',
        'document_number',
        'file_path',
        'uploaded_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}






