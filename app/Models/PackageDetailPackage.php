<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDetailPackage extends Model
{
    use HasFactory;

    protected $table = 'package_detail_packages';

    protected $fillable = [
        'package_id',
        'package_detail_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function detail()
    {
        return $this->belongsTo(PackageDetail::class, 'package_detail_id');
    }
}
