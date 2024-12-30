<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'address',
        'district_id',
        'postal_code',
        'no_hp',
        'description',
        'latitude',
        'longitude',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }
}
