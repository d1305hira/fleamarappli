<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_ログインユーザーが商品購入できる()
    {
        $user = User::factory()->create([
            'postal_code' => '111-1111',
            'address' => 'テスト市テスト町',
            'building' => 'テストビル',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        // セッションに配送先を保存（通常は住所変更画面で保存される）
        session([
            'checkout.shipping_postal_code' => '111-1111',
            'checkout.shipping_address' => 'テスト市テスト町',
            'checkout.shipping_building' => 'テストビル',
        ]);

        $formData = [
            'item_id' => $item->id,
            'payment_method' => 1,
        ];

        $response = $this->post(route('checkout'), $formData);

        $response->assertRedirect();
        $this->assertStringContainsString('https://checkout.stripe.com', $response->headers->get('Location'));

        // Stripe決済完了をシミュレート
$response = $this->get(route('checkout.success'));


        // 購入情報がデータベースに保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_postal_code' => '111-1111',
            'shipping_address' => 'テスト市テスト町',
            'shipping_building' => 'テストビル',
            'payment_method' => 1,
        ]);
    }


    public function test_購入後の商品が一覧画面でsold表示される()
		{
    // ユーザー作成＆ログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 商品作成
    $item = Item::factory()->create([
        'is_sold' => false, // 初期状態
    ]);

    // 購入履歴を作成（これが isSold() 判定の根拠になる）
    Purchase::create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'shipping_postal_code' => '111-1111',
        'shipping_address' => 'テスト市テスト町',
        'shipping_building' => 'テストビル',
        'purchased_at' => now(),
        'payment_method' => 1,
  ]);

		$item->update(['is_sold' => true]);

    // 商品一覧画面を表示
    $response = $this->get(route('top'));

    // sold 表示を確認（is_sold が true になるはず）
    $response->assertStatus(200);
    $response->assertSeeText('sold');
}

public function test_購入後の商品がプロフィール画面に表示される()
{
    // 出品者と購入者を分けることで一覧フィルターに対応
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $this->actingAs($buyer);

    // 商品作成（出品者のもの）
    $item = Item::factory()->create([
        'user_id' => $seller->id,
        'name' => 'テスト商品',
    ]);

    // 購入履歴を作成（buyerがsellerの商品を購入）
    Purchase::create([
        'user_id' => $buyer->id,
        'item_id' => $item->id,
        'shipping_postal_code' => '111-1111',
        'shipping_address' => 'テスト市テスト町',
        'shipping_building' => 'テストビル',
        'purchased_at' => now(),
        'payment_method' => 1,
    ]);

    // プロフィール画面（購入済みタブ）を表示
    $response = $this->get(route('profile', ['tab' => 'purchased']));

    // 商品名が表示されていることを確認
    $response->assertStatus(200);
    $response->assertSeeText('テスト商品');
}


		/** @test */
    public function test_支払い方法が注文概要に反映される()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'checkout.payment_method' => 2,
        ])
        ->get(route('purchase.show', ['item' => $item->id]))
        ->assertStatus(200)
        ->assertSee('<option value="2" selected>', false) // 左側の選択状態
        ->assertSee('カード支払い'); // 右側の注文概要に表示されているか
}

}