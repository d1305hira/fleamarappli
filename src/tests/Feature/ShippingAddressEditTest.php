<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ShippingAddressEditTest extends TestCase
{
    use RefreshDatabase;

public function test_住所変更後に購入画面に反映される()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user);

    // 住所変更フォームを送信
    $this->post(route('shipping_address.update', ['item' => $item->id]), [
        'postal_code' => '222-2222',
        'address' => 'テスト市テスト町',
        'building' => 'テストビル',
    ]);

    // 購入画面を表示
    $response = $this->get(route('purchase.show', ['item' => $item->id]));

    // 反映されているか確認
    $response->assertStatus(200);
    $response->assertSee('〒222-2222 テスト市テスト町 テストビル');
}

// 住所変更後に購入処理を行い、Purchaseに保存されるか確認
public function test_購入時に送付先住所がPurchaseに保存される()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user);

    // セッションに購入情報をセット
    $this->withSession([
        'checkout.item_id' => $item->id,
        'checkout.payment_method' => 1,
        'checkout.shipping_postal_code' => '111-1111',
        'checkout.shipping_address' => 'テスト市テスト町',
        'checkout.shipping_building' => 'テストビル',
    ])->get('/checkout/success');

    // DBに保存されたか確認
    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'shipping_postal_code' => '111-1111',
        'shipping_address' => 'テスト市テスト町',
        'shipping_building' => 'テストビル',
    ]);
}
}