@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_shipping.css') }}">
@endsection

@section('content')
<div class="container" style="max-width: 500px;">
    <h2 class="text-center mb-4">商品の出品</h2>
      <div class="profiles">

      <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div class="form-group mb-4 item_image">
          <label for="image" class="form-label fw-semibold">商品画像</label>
            <div class="image-upload-box text-center">
              <label for="image" class="btn btn-outline-danger">画像を選択する</label>
              <input type="file" id="image" name="image" class="d-none @error('image') is-invalid @enderror" accept="image/*">
              <img id="preview" src="#" alt="プレビュー画像" class="img-preview mt-3 d-none">
              @error('image')
              <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>
        </div>

        <!-- 商品の詳細 -->
        <div class="form-group mb-3">
          <label for="details" class="form-label">カテゴリー</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach ($categories as $category)
              <input type="checkbox" class="btn-check @error('category_id') is-invalid @enderror" name="category_id[]" id="category{{$category->id}}" value="{{ $category->id }}" {{ is_array(old('category_id')) && in_array($category->id, old('category_id')) ? 'checked' : '' }}>
                <label class="btn category-tag" for="category{{ $category->id }}">{{ $category->name }}</label>
              @endforeach
            </div>
            @error('category_id')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div class="form-group mb-3">
          <label for="condition">商品の状態</label>
            <select id="condition"  class="form-control @error('condition') is-invalid @enderror" name="condition">
            <option value="">選択してください</option>
            <option value="1" {{ old('condition') == '1' ? 'selected' : ''}}>良好</option>
            <option value="2" {{ old('condition') == '2' ? 'selected' : ''}}>目立った傷や汚れなし</option>
            <option value="3" {{ old('condition') == '3' ? 'selected' : ''}}>やや傷や汚れあり</option>
            <option value="4" {{ old('condition') == '4' ? 'selected' : ''}}>状態が悪い</option>
            </select>
            @error('condition')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- 商品名と説明 -->
        <div class="form-group mb-3">
            <label>商品名と説明</label>
            <div class="item_name">
              <label for="name">商品名</label>
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" >
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="item_brand">
              <label for="brand">ブランド名</label>
              <input id="brand" type="text" class="form-control @error('brand') is-invalid @enderror" name="brand" value="{{old('brand')}} " >
                  @error('brand')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
            </div>
            <div class="form-group mb-3 item_description">
              <label for="description" class="form-label">商品の説明</label>
              <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                  @error('description')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
            </div>
            <div class="form-group mb-3 item_price position-relative">
              <label for="price" class="form-label">販売価格</label>
              <input id="price" type="number" class="form-control price-input @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}">
              @error('price')
              <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>
        </div>

        <!-- 更新ボタン -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-danger">出品する</button>
        </div>
      </form>
      </div>
</div>
@endsection