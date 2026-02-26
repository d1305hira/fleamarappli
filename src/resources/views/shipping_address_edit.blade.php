@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shipping_address_edit.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>住所の変更</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('shipping_address.update', ['item' => $item->id]) }}" method="POST">
        @csrf

        <a href="{{ route('purchase.show', ['item' => $item->id]) }}" class="btn btn-secondary mb-3">戻る</a>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ $user->postal_code }}">
            @error('postal_code')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ $user->address }}">
            @error('address')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" class="form-control" value="{{ $user->building }}">
            @error('building')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger mt-3">送付先を更新する</button>
    </form>
</div>
@endsection