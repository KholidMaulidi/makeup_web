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

    public function details()
    {
        return $this->belongsToMany(PackageDetail::class, 'package_detail_packages');
    }

    public function requestPackages()
    {
        return $this->hasMany(RequestPackage::class, 'package_id');
    }
}
