<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'address',
        'province',
        'city',
        'subdistrict',
        'postal_code',
        'no_hp',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
