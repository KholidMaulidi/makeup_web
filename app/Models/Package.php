<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'package_name',
        'visit_type',
        'price',
        'mua_id'
    
    ];

    public function mua()
    {
        return $this->belongsTo(User::class, 'mua_id');
    }

    public function request()
    {
        return $this->hasMany(Request::class, 'package_id');
    }
}
