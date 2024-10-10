<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'mua_id',
        'type_id',
        'payment_method_name',
        'payment_method_number',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function type()
{
    return $this->belongsTo(PaymentMethodType::class, 'type_id');
}
}
