<p>{{ $buyer->name }} さんが取引を完了しました。</p>

<p>商品名：{{ $item->name }}</p>
<p>価格：¥{{ number_format($item->price) }}</p>

<p>取引画面はこちら：</p>
<p>
  <a href="{{ route('transaction.show', $item->id) }}">
    {{ route('transaction.show', $item->id) }}
  </a>
</p>
