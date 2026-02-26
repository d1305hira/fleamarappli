<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemComment;
use App\Http\Requests\CommentRequest;

class ItemCommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
      $request->validate([
        'comment' => 'required|string|max:255',
        ]);

      $item->comments()->create([
        'user_id' => auth()->id(), // ログインユーザー前提
        'comment' => $request->comment,
        ]);

      return redirect()->route('top')->with('success', 'コメントを投稿しました');
    }
}
