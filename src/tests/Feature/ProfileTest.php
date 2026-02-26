<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** 必要な情報が取得できる */
    /** @test */
    public function profile_page_displays_user_info_listed_and_purchased_items()
    {
        $user = User::factory()->create([
            'name' => 'testman',
            'image' => 'profile.jpg',
        ]);

        // 出品商品
        $listedItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '商品A',
            'image' => 'itemA.jpg',
        ]);

        // 購入商品
        $purchasedItem = Item::factory()->create([
            'name' => '購入商品X',
            'image' => 'itemX.jpg',
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'purchased_at' => now(),
            'payment_method' => '2',
        ]);

        // 出品商品タブ（デフォルト）
        $this->actingAs($user)
            ->get(route('profile'))
            ->assertStatus(200)
            ->assertSee('testman')
            ->assertSee('profile.jpg')
            ->assertSee('商品A')
            ->assertSee('itemA.jpg')
            ->assertDontSee('購入商品X'); // 購入商品は表示されない

        // 購入商品タブ
        $this->actingAs($user)
            ->get(route('profile', ['tab' => 'purchased']))
            ->assertStatus(200)
            ->assertSee('購入商品X')
            ->assertSee('itemX.jpg')
            ->assertDontSee('商品A'); // 出品商品は表示されない
    }


    /** 変更項目が初期値として表示される */
    /** @test */
public function profile_edit_page_displays_updated_user_info_as_initial_values()
{
  Storage::fake('public');
    $user = User::factory()->create([
        'name' => 'testman',
        'image' => 'old.jpg',
        'postal_code' => '123-4567',
        'address' => '旧市旧町',
    ]);

    // プロフィール更新（POST）
    $this->actingAs($user)
        ->post(route('profile.update'), [
            'name' => 'testman2',
            'postal_code' => '987-6543',
            'address' => '新市新町1-2-3',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

    // 編集画面を再度開く
    $this->get(route('profile.edit'))
        ->assertStatus(200)
        ->assertSee('testman2')
        ->assertSee('987-6543')
        ->assertSee('新市新町1-2-3')
        ->assertSee($user->fresh()->image);
      }
}
