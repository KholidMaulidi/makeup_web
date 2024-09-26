<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRequest extends Model
{
    use HasFactory;

    protected $table = 'history_requests';

    protected $casts = [
        'date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    protected $fillable = [
        'id_user',
        'id_mua',
        'package_id',
        'date',
        'start_time',
        'end_time', 
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function mua()
    {
        return $this->belongsTo(User::class, 'id_mua');
    }
}
