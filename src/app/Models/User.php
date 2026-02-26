<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'postal_code',
        'address',
        'building',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function likedItems()
    {
    return $this->belongsToMany(Item::class, 'item_likes')->withTimestamps();
    }

    public function isProfileComplete()
    {
    return $this->postal_code && $this->address && $this->building;
    }

    public function listedItems()
    {
    return $this->hasMany(Item::class);
    }

    public function addresses()
    {
    return $this->hasMany(Address::class);
    }

    public function purchases()
    {
    return $this->hasMany(Purchase::class);
    }

    public function receivedRatings()
    {
    return $this->hasMany(Rating::class, 'rated_user_id');
    }
}
