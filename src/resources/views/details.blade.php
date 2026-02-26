@extends('layouts.app')

@section('title', $item->name . 'の詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/details.css') }}">
@endsection

@section('content')
  <div class="item_detail d-flex flex-wrap gap-4">
  <!-- 左：画像 -->
  <div class="item_detail_image flex-shrink-0">
    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="img-fluid rounded">
  </div>

    <!-- 右：商品情報 -->
  <div class="item_detail_info d-flex flex-column justify-content-start">
    <h1 class="fw-bold mb-2">{{ $item->name }}</h1>
    <p class="text-muted mb-1">{{ $item->brand }}</p>
    <p class="fs-4 text-danger mb-3">¥{{ number_format($item->price) }}（税込み）</p>

    <!-- いいねボタン -->
      <form method="POST" action="{{ route('item.like', $item->id) }}">
      @csrf
        @php
          $liked = $item->likes->contains('user_id', auth()->id());
        @endphp
      <button type="submit" class="btn btn-sm {{ $liked ? 'btn-warning' : 'btn-outline-danger' }}">
        {{ $liked ? '★' : '☆' }} {{ $item->likes_count }}
      </button>
        <span class="btn btn-outline-secondary btn-sm disabled">
        💬 {{ $item->comments->count() }}
        </span>
      </form>

    <!-- 購入手続きへボタン -->
      <a href="{{ route('purchase.show', $item->id) }}" class="btn btn-danger mb-3">購入手続きへ</a>

    <!-- 商品説明 -->
    <div class="item_description mb-4">
  <h2>商品説明</h2>
  <p>{{ $item->description }}</p>
</div>

    <!-- 商品の情報 -->
    <div class="item_block_info mb-4">
  <h2>商品の情報</h2>
  <p><strong>カテゴリー：</strong>
    @forelse ($item->categories as $category)
      <span class="badge bg-secondary">{{ $category->name }}</span>
    @empty
      <span class="text-muted">未設定</span>
    @endforelse
  </p>
  <p><strong>商品の状態：</strong>{{ config('select_options.item_conditions')[$item->condition] ?? '不明' }}</p>
</div>

    <!-- コメントセクション -->
    <div class="comments_section mb-4">
  <h3>コメント ({{ $item->comments->count() }})</h3>
  @forelse ($item->comments as $comment)
    <div class="comment mb-2">
      <strong>{{ $comment->user->name ?? '匿名' }}</strong>：
      <p>{{ $comment->comment }}</p>
    </div>
  @empty
    <p class="text-muted">まだコメントはありません。</p>
  @endforelse
</div>

  <!-- コメント投稿フォーム（常時表示） -->
<form method="POST" action="{{ route('comments.store', $item->id) }}" class="mb-5">
  @csrf
  <label for="comment" class="form-label">商品へのコメント</label>
  <textarea name="comment" id="comment" class="form-control mb-2" rows="4" placeholder="こちらにコメントを入力"></textarea>

  @error('comment')
  <div class="text-danger small mb-2">{{ $message }}</div>
  @enderror

  <button type="submit" class="btn btn-danger">コメントを送信する</button>
</form>
  </div>
@endsection
