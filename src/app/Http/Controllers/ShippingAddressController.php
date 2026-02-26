<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ShippingAddressRequest;
use App\Models\Item;

class ShippingAddressController extends Controller
{
    public function edit(Item $item)
    {
        $user = auth()->user();
        return view('shipping_address_edit', compact('user', 'item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'postal_code' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $paymentMethod = session('checkout.payment_method');

        session([
          'checkout.payment_method' => $paymentMethod,
          'checkout.shipping_postal_code' => $request->postal_code,
          'checkout.shipping_address' => $request->address,
          'checkout.shipping_building' => $request->building,
        ]);

        return redirect()->route('purchase.show', ['item' => $item->id])
          ->with('success', '送付先を更新しました');
    }
}
