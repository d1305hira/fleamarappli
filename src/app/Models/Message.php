<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'to_user_id',
        'message',
        'image',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function isOwnedBy($user)
    {
    return $this->user_id === $user->id;
    }
}
