<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\ItemComment;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;


class ItemController extends Controller
{
    public function index(Request $request)
    {
      $tab = $request->query('tab', 'recommended');
      $keyword = $request->input('keyword');

      $likedItems = collect();
$isLoggedIn = auth()->check(); // 1回だけ判定してキャッシュ

if ($isLoggedIn) {
    $likedItems = auth()->user()->likedItems()->latest()->get();
}

$items = Item::query()
    ->when(!empty($keyword), function ($query) use ($keyword) {
        $query->where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->orWhere('brand', 'like', "%{$keyword}%");
    });

if ($isLoggedIn) {
    $items->where('user_id', '!=', auth()->id());
}

$items = $items->with(['purchase'])
    ->withCount('likes')
    ->latest()
    ->get();

return view('top', compact('tab', 'items', 'likedItems', 'keyword'));

    }

    public function show($itemId)
    {
      $item = Item::with(['comments', 'categories', 'user'])->withCount('likes')->findOrFail($itemId);

      return view('details', compact('item'));
    }

    public function create()
    {
      $categories= Category::all();
      $selectedCategory=null;

      return view('item_shipping',compact('categories','selectedCategory'));
    }

    public function store(ExhibitionRequest $request)
    {
      $validated = $request->validated(); // ExhibitionRequest の rules() に従ってバリデーション済み

      $imagePath = null;
      if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('items', 'public');
        }

      $item=Item::create([
        'user_id' => auth()->id(),
        'name' => $validated['name'],
        'brand' => $validated['brand'],
        'description' => $validated['description'],
        'price' => $validated['price'],
        'condition' => $validated['condition'],
        'image' => $imagePath,
        ]);

      $item->categories()->sync($validated['category_id']);

      return redirect()->route('profile')->with('success', '商品を出品しました');
    }

    public function toggle(Item $item)
    {
      $user = auth()->user();

      if ($user->likedItems()->where('item_id', $item->id)->exists()) {
        // すでにいいね済み → 削除（論理削除ではなく detach）
        $user->likedItems()->detach($item->id);
      } else {
        // 未いいね → 追加
        $user->likedItems()->attach($item->id);
        }

    return back();
    }

    public function search(Request $request)
{
    $keyword = $request->input('keyword');
    $isLoggedIn = auth()->check();

    $items = Item::query();

    if ($isLoggedIn) {
        $items->where('user_id', '!=', auth()->id());
    }

    $items->where(function ($query) use ($keyword) {
        $query->where('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->orWhere('brand', 'like', "%{$keyword}%");
    });

    $items = $items->with(['purchase'])
        ->withCount('likes')
        ->latest()
        ->get();

    return view('search_results', compact('items', 'keyword'));
}


}
