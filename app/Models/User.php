<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Review;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute()
    {
        if (empty($this->attributes['avatar'])) {
            return null;
        }
        return url('/storage/images/avatars/' . $this->attributes['avatar']);
    }

    public function role(){    
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function makeupArtistProfile(){
        return $this->hasOne(MakeupArtistProfile::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'id_user');
    }
    public function packages()
    {
        return $this->hasMany(Package::class, 'mua_id');
    }

    public function offDays()
    {
        return $this->hasMany(DayOff::class, 'id_mua');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'mua_id');
    }

    public function paymentMethod(){
        return $this->hasMany(PaymentMethod::class, 'mua_id');
    }
}
