<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    public function paymentMethod(){
        return $this->hasMany(PaymentMethod::class);
    }
}
