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
        'description',
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

    public function details()
    {
        return $this->hasMany(PackageDetail::class, 'package_id');
    }
}
