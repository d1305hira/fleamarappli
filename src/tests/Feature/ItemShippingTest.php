<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;

class ItemShippingTest extends TestCase
{
    use RefreshDatabase;

    /** 商品出品画面にて必要な情報が保存できる */
    /** @test */
    public function item_shipping_test()
    {
        Storage::fake('public');

        // 1. ログインユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. カテゴリ作成
        $categories = Category::factory()->count(2)->create();

        // 3. 入力データ準備
        $formData = [
            'image' => UploadedFile::fake()->image('item.jpg'),
            'category_id' => $categories->pluck('id')->toArray(),
            'condition' => '1',
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これは説明文です',
            'price' => 2500,
        ];

        // 4. POST送信
        $response = $this->post(route('items.store'), $formData);

        // 5. リダイレクト確認（成功時の遷移先に応じて調整）
        $response->assertRedirect(); // 例: route('items.index') など

        // 6. DB保存確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 2500,
            'user_id' => $user->id,
        ]);

        // 7. カテゴリ紐付け確認（中間テーブル）
        $item = Item::where('name', 'テスト商品')->first();
        foreach ($categories as $category) {
            $this->assertTrue($item->categories->contains($category));
        }

        // 8. 画像保存確認（Storageにファイルがあるか）
        Storage::disk('public')->assertExists($item->image);
    }
}
