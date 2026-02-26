<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** いいねアイコンの押下でいいねした商品として登録できる */
    /** @test */
    public function testLogged_in_user_can_like_item()
    {
      $user = User::factory()->create();
      $item = Item::factory()->create();

      // ① ログイン
      $this->actingAs($user);

      // ② 詳細ページを開く（オプション：表示確認）
      $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('☆');

      // ③ いいねアイコンを押下（POST送信）
      $this->post(route('item.like', $item->id))
        ->assertRedirect();

      // DBにいいねが保存されていることを確認
      $this->assertDatabaseHas('item_likes', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        ]);
    }


    /** 追加済みのアイコンは色が変化する */
    /** @test */
    public function Logged_in_user_can_like_item_iconchanges()
    {
      $user = User::factory()->create();
      $item = Item::factory()->create();

      // ① ログイン
      $this->actingAs($user);

      // ② 詳細ページを開く（オプション：表示確認）
      $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('☆');

      // ③ いいねアイコンを押下（POST送信）
      $this->post(route('item.like', $item->id))
        ->assertRedirect();

      // 追加済のアイコンの色が変化していることを確認
      $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('★');
    }


    /** 再度いいねアイコンを押下することによって、いいねを解除することができる */
    /** @test */
    public function Logged_in_user_can_like_item_iconrechange()
    {
      $user = User::factory()->create();
      $item = Item::factory()->create();

      // ① ログイン
      $this->actingAs($user);

      // 初期状態：未いいね
    $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('☆');

    // ① いいねする
    $this->post(route('item.like', $item->id))
        ->assertRedirect();

    $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('★');

    // ② 再度押下 → いいね解除
    $this->post(route('item.like', $item->id))
        ->assertRedirect();

    $this->get(route('item.show', $item->id))
        ->assertStatus(200)
        ->assertSee('☆');

    // DBからも削除されていることを確認
    $this->assertDatabaseMissing('item_likes', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        ]);
  }
}
