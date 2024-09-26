<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPackage extends Model
{
    use HasFactory;

    protected $table = 'request_packages';

    protected $fillable = [
        'request_id',
        'package_id',
        'quantity',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
