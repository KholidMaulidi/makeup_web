<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';
    protected $guarded = [];
    public $timestamps = false;

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }
}
