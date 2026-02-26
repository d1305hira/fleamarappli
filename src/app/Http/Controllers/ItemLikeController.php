<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemLike;

class ItemLikeController extends Controller
{
    public function toggle(Item $item)
    {
        $user = auth()->user();

        if ($user->likedItems()->where('item_id', $item->id)->exists()) {
          // すでにいいね済み → 解除（物理削除）
          $user->likedItems()->detach($item->id);
          } else {
          // 未いいね → 新規追加
          $user->likedItems()->attach($item->id);
          }

        return back()->with('success', 'いいねを更新しました');
    }
}