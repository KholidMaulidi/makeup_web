<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'payment_status',
        'payment_proof',
        'status',
        'updated_at',
    ];

    public function getPaymentProofAttribute()
    {
        if (empty($this->attributes['payment_proof'])) {
            return null;
        }
        return url('/storage/images/' . $this->attributes['payment_proof']);
    }
    
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
