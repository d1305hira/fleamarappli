<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\ItemComment;
use Illuminate\Support\Facades\Storage;

class DetailsTest extends TestCase
{
    use RefreshDatabase;

    /** 必要な情報が表示される */
    /** @test */
    public function Item_detail_page_displays_all_required_information()
    {
      $user = User::factory()->create();
      $otherUser = User::factory()->create();
      $categories = Category::factory()->count(2)->create();

      // 商品作成
      $item = Item::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'テスト商品',
        'brand' => 'テストブランド',
        'image' => 'test.jpg',
        'condition' => 1, // configで定義されているキー
        'description' => 'これはテスト用の商品説明です。',
        ]);
      $item->categories()->attach($categories->pluck('id'));

      $comment = ItemComment::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'comment' => 'これはテストコメントです。',
        ]);

      // 商品詳細ページにアクセス
      $response = $this->actingAs($user)->get(route('item.show', $item->id));

      // ステータス確認
      $response->assertStatus(200);

      // 商品名、ブランド名、価格、画像、説明
      $response->assertSee($item->name);
      $response->assertSee($item->brand);
      $response->assertSee(Storage::url($item->image));
      $response->assertSee($item->description);

      // コメント数と内容
      $response->assertSee('コメント (' . $item->comments->count() . ')');
      $response->assertSee($comment->comment);
      $response->assertSee($user->name);

      // カテゴリ名と商品の状態
      foreach ($categories as $category) {
        $response->assertSee($category->name);
        }
      $response->assertSee(config('select_options.item_conditions')[$item->condition]);

      // いいね数（★/☆）とコメント数ボタン
      $response->assertSee('☆'); // 初期状態（未いいね）
      $response->assertSee('💬 ' . $item->comments->count());
    }

    /** 複数選択されたカテゴリが表示されているか */
    /** @test */
    public function Item_detail_page_displays_multiple_categories()
    {
      $user = User::factory()->create();
      $categories = Category::factory()->count(3)->create();

      $item = Item::factory()->create([
        'user_id' => $user->id,
        'name' => '複数カテゴリ商品',
        'description' => 'カテゴリが複数ある商品です',
        'image' => 'test.jpg',
        'condition' => 1,
        ]);

      // 複数カテゴリを紐づけ
      $item->categories()->attach($categories->pluck('id'));

      $response = $this->actingAs($user)->get(route('item.show', $item->id));

      $response->assertStatus(200);

      // 各カテゴリ名が表示されているか確認
      foreach ($categories as $category) {
        $response->assertSee($category->name);
        }
    }
}