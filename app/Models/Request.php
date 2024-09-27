<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';
    protected $primaryKey = 'id';
    protected $casts = [
        'date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected $fillable = [
        'id_user',
        'id_mua',
        'date',
        'start_time',
        'end_time',
        'distance',
        'visit_type',
        'postage',
        'total_price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function mua()
    {
        return $this->belongsTo(User::class, 'id_mua');
    }

    public function requestPackages()
    {
        return $this->hasMany(RequestPackage::class, 'request_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}

