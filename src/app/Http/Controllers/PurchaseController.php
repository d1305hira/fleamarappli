<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
  public function show(Item $item)
	{
    $user = auth()->user();

    $shipping_address = $user;

    $payment_methods = Purchase::PAYMENT_METHODS;

    return view('purchase', compact('user', 'item', 'shipping_address', 'payment_methods'));
	}

  public function checkout(PurchaseRequest $request)
  {
    $validated = $request->validated();

    Stripe::setApiKey(config('services.stripe.secret'));
    $item = Item::findOrFail($validated['item_id']);

    // セッションに保存
    session([
						'checkout.item_id' => $item->id,
     				'checkout.payment_method' => $validated['payment_method'],
        		]);

    $session = Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [[
      'price_data' => [
      'currency' => 'jpy',
      'product_data' => ['name' => $item->name],
      'unit_amount' => $item->price,
      ],
      'quantity' => 1,
            ]],
      'mode' => 'payment',
      'success_url' => url('/checkout/success'),
      'cancel_url' => route('checkout.cancel'),
        ]);

      return redirect($session->url);
  }

    public function success(Request $request)
		{
    $user = auth()->user();

    $itemId = session('checkout.item_id');
    $paymentMethod = session('checkout.payment_method');
    $item = Item::findOrFail($itemId);

    if ($item->isSold()) {
        return redirect()->route('top')->with('error', 'この商品はすでに購入されています。');
    }

    $item->is_sold = 1;
    $item->save();

    Purchase::create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'shipping_postal_code' => session('checkout.shipping_postal_code', $user->postal_code),
        'shipping_address' => session('checkout.shipping_address', $user->address),
        'shipping_building' => session('checkout.shipping_building', $user->building),
        'recipient_name' => $user->name,
        'purchased_at' => now(),
        'payment_method' => $paymentMethod,
    ]);

    session()->forget([
        'checkout.item_id',
        'checkout.payment_method',
        'checkout.shipping_postal_code',
        'checkout.shipping_address',
        'checkout.shipping_building',
    ]);

    return redirect()->route('top')->with('success', '購入が完了しました！');
		}

    public function cancel()
    {
        return redirect()->route('top')->with('error', '購入がキャンセルされました。');
    }
}
