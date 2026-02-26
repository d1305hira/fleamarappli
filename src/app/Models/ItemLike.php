<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLike extends Model
{

  protected $table = 'item_likes';

  protected $fillable = [
  'user_id',
  'item_id',
  ];

  public function item()
  {
  return $this->belongsTo(Item::class);
  }

  public function user()
  {
  return $this->belongsTo(User::class);
  }

}
