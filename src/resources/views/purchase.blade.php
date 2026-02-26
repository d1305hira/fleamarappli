@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="{{ route('checkout') }}" method="POST">
    @csrf
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    <input type="hidden" name="shipping_address_id" value="{{ $shipping_address->id }}">

    <div class="purchase-container">
        <div class="purchase-left">
            <!-- 商品情報 -->
            <div class="product-info section">
                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="product-image">
                <div class="product-details">
                    <h2>{{ $item->name }}</h2>
                    <p class="price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <!-- 支払い方法 -->
            <div class="payment-method section @if ($errors->has('payment_method')) has-error @endif">
              <h3>支払い方法</h3>
            	@php
    					$selected_method = old('payment_method') ?? session('checkout.payment_method');
							@endphp
                <select name="payment_method" id="payment-method">
    						<option value="" {{ empty($selected_method) ? 'selected' : '' }}>選択してください</option>
    						@foreach($payment_methods as $key => $method)
        				<option value="{{ $key }}" {{ $selected_method == $key ? 'selected' : '' }}>{{ $method }}</option>
    						@endforeach
								</select>

                @if ($errors->has('payment_method'))
                    <div class="error-message">{{ $errors->first('payment_method') }}</div>
                @endif
            </div>

						@php
    				$postal_code = session('checkout.shipping_postal_code') ?: $user->postal_code;
    				$address = session('checkout.shipping_address') ?: $user->address;
    				$building = session('checkout.shipping_building') ?: $user->building;
						@endphp

            <!-- 配送先 -->
            <div class="shipping-address section">
                <h3>配送先</h3>
                <p>
                    〒{{ $postal_code }} {{ $address }} {{ $building }}
                </p>
                <a href="{{ route('shipping_address.edit', ['item' => $item->id]) }}" class="change-link">変更する</a>
            </div>
        </div>

        <div class="purchase-right">
            <!-- 注文概要 -->
            <div class="summary-box section">
                <div class="summary-item">
                    <h4>商品代金</h4>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>

                <div class="divider-line"></div>

                @php
  							$selected_method = old('payment_method') ?? session('checkout.payment_method');
								@endphp

                <div class="summary-item">
                    <h4>支払い方法</h4>
                    <p id="selected-method">未選択</p>
                </div>
            </div>

            <!-- 購入ボタン -->
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('payment-method');
        const display = document.getElementById('selected-method');

        select.addEventListener('change', function () {
            const selectedText = select.options[select.selectedIndex].text || '未選択';
            display.textContent = selectedText;
        });
    });
</script>
@endsection
