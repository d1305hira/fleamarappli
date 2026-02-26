<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    const PAYMENT_METHODS = [
        1 => 'コンビニ払い',
        2 => 'カード支払い',
    ];

    // マスアサインメント許可フィールド
    protected $fillable = [
        'user_id',
    'item_id',
    'price',
    'purchased_at',
    'payment_method',
    'shipping_postal_code',
    'shipping_address',
    'shipping_building',
    ];

    // 購入された商品とのリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
