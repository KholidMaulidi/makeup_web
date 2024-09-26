<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayOff extends Model
{
    use HasFactory;

    protected $table = 'day_offs';

    protected $casts = [
        'date' => 'date', 
    ];

    protected $fillable = [
        'id_mua',
        'date',
    ];

    public function mua()
    {
        return $this->belongsTo(User::class, 'id_mua');
    }
}
