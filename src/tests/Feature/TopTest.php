<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Facades\Storage;

class TopTest extends TestCase
{
    use RefreshDatabase;

    /** 全商品を取得できる */
    /** @test */
    public function Top_page_displays_multiple_items_correctly()
      {
        // ユーザーとカテゴリを事前に作成
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->create();

        // アイテムを3件作成し、ユーザーとカテゴリを紐づけ
        $items = Item::factory()->count(3)->create(['user_id' => $user->id]);
        foreach ($items as $item) {
            $item->categories()->attach($categories->pluck('id'));
        }

        // トップページにアクセス（おすすめタブ）
        $response = $this->get(route('top', ['tab' => 'recommended']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 各アイテムの表示確認（nameとimage）
        foreach ($items as $item) {
            $response->assertSee($item->name);
            $response->assertSee(Storage::url($item->image));
        }
      }


      /** 購入済み商品は「Sold」と表示される */
      /** @test */
      public function Purchased_item_displays_sold_label_on_top_page()
      {
        // ユーザーとカテゴリを作成
        $user = User::factory()->create();

        // カテゴリを複数作成
        $categories = Category::factory()->count(2)->create();

        // アイテムを1件作成
        $item = Item::factory()->create([
          'user_id' => $user->id,
          'name' => '購入済み商品',
          'image' => 'sold.jpg',
          ]);
        $item->categories()->attach($categories->pluck('id'));

        // 購入済み状態を再現
        Purchase::create([
          'user_id' => $user->id,
          'item_id' => $item->id,
          'shipping_address_id' => null, // 必要ならSeederで事前に作成
          'purchased_at' => now(),
          'payment_method' => rand(1, 2),
          ]);

        // トップページにアクセス（おすすめタブ）
        $response = $this->get(route('top', ['tab' => 'recommended']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 商品名と画像が表示されているか
        $response->assertSee($item->name);
        $response->assertSee(Storage::url($item->image));

        // soldラベルが表示されているか
        $response->assertSee('sold');
      }


      /** 自分が出品した商品は表示されない */
      /** @test */
      public function Top_page_does_not_display_items_listed_by_logged_in_user()
      {
        // ログインユーザーを作成
        $user = User::factory()->create();

        // 他のユーザーを作成
        $otherUser = User::factory()->create();

        // カテゴリを作成
        $categories = Category::factory()->count(2)->create();

        // ログインユーザーが出品した商品
        $ownItem = Item::factory()->create([
          'user_id' => $user->id,
          'name' => '自分の商品',
          'image' => 'own.jpg',
          ]);
        $ownItem->categories()->attach($categories->pluck('id'));

        // 他のユーザーが出品した商品
        $otherItem = Item::factory()->create([
          'user_id' => $otherUser->id,
          'name' => '他人の商品',
          'image' => 'other.jpg',
        ]);
        $otherItem->categories()->attach($categories->pluck('id'));

        // ログイン処理
        $this->actingAs($user);

        // トップページにアクセス（おすすめタブ）
        $response = $this->get(route('top', ['tab' => 'recommended']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 自分の商品が表示されていないことを確認
        $response->assertDontSee($ownItem->name);
        $response->assertDontSee(Storage::url($ownItem->image));

        // 他人の商品は表示されていることを確認
        $response->assertSee($otherItem->name);
        $response->assertSee(Storage::url($otherItem->image));
      }


      /** いいねした商品だけが表示される */
      /** @test */
      public function My_list_page_displays_items_with_conditions()
      {
        // ログインユーザー作成
        $user = User::factory()->create();

        // 他のユーザーも作成
        $otherUser = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // アイテム作成
        $likedItem = Item::factory()->create([
          'user_id' => $otherUser->id,
          'name' => 'いいねした商品',
          'image' => 'liked.jpg',
        ]);

        $unlikedItem = Item::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'いいねしてない商品',
        'image' => 'unliked.jpg',
        ]);

        // いいね（中間テーブルに登録）
        $user->likedItems()->attach($likedItem->id);

        // マイリストタブにアクセス
        $response = $this->get(route('top', ['tab' => 'mylist']));

        // ステータス確認
        $response->assertStatus(200);

        // いいねした商品が表示されていること
        $response->assertSee($likedItem->name);
        $response->assertSee(Storage::url($likedItem->image));

        // いいねしてない商品は表示されないこと
        $response->assertDontSee($unlikedItem->name);
        $response->assertDontSee(Storage::url($unlikedItem->image));
      }


      /** 購入済み商品は「Sold」と表示される */
      /** @test */
      public function Purchased_item_displays_sold_label_on_top_mylist()
      {
        // ユーザーとカテゴリを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // カテゴリを複数作成
        $categories = Category::factory()->count(2)->create();

        // アイテムを1件作成
        $item = Item::factory()->create([
          'user_id' => $user->id,
          'name' => '購入済み商品',
          'image' => 'sold.jpg',
          ]);
        $item->categories()->attach($categories->pluck('id'));

        // 購入済み状態を再現
        Purchase::create([
          'user_id' => $user->id,
          'item_id' => $item->id,
          'shipping_address_id' => null, // 必要ならSeederで事前に作成
          'purchased_at' => now(),
          'payment_method' => rand(1, 2),
          ]);

        // いいね（中間テーブルに登録）
        $user->likedItems()->attach($item->id);

        // マイリストタブにアクセス
        $this->actingAs($user);
        $response = $this->get(route('top', ['tab' => 'mylist']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 商品名と画像が表示されているか
        $response->assertSee($item->name);
        $response->assertSee(Storage::url($item->image));

        // soldラベルが表示されているか
        $response->assertSee('sold');
      }


      /** 未認証の場合は何も表示されない */
      /** @test */
      public function Mylist_tab_does_not_display_items_when_not_logged_in()
      {
        // ユーザーとカテゴリを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // カテゴリを複数作成
        $categories = Category::factory()->count(2)->create();

        // アイテムを1件作成
        $item = Item::factory()->create([
          'user_id' => $user->id,
          'name' => '購入済み商品',
          'image' => 'sold.jpg',
          ]);
        $item->categories()->attach($categories->pluck('id'));

        // アイテム作成
        $likedItem = Item::factory()->create([
          'user_id' => $otherUser->id,
          'name' => 'いいねした商品',
          'image' => 'liked.jpg',
          ]);

        // 購入済み状態を再現
        Purchase::create([
          'user_id' => $user->id,
          'item_id' => $item->id,
          'shipping_address_id' => null, // 必要ならSeederで事前に作成
          'purchased_at' => now(),
          'payment_method' => rand(1, 2),
          ]);

        // いいね（中間テーブルに登録）
        $user->likedItems()->attach($item->id);

        // マイリストタブにアクセス
        $response = $this->get(route('top', ['tab' => 'mylist']));

        // ステータスコード確認
        $response->assertStatus(200);

        // ログイン促し文言が表示されること
        $response->assertSee('マイリストを表示するにはログインが必要です。');

        // 商品名や画像が表示されないこと
        $response->assertDontSee($item->name);
        $response->assertDontSee(Storage::url($item->image));
      }


      /** 「商品名」で部分一致検索ができる */
      /** @test */
      public function Top_page_displays_items_matching_keyword_partially()
      {
        // ユーザーとカテゴリを作成
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->create();

        // 検索にヒットする商品
        $matchedItem = Item::factory()->create([
          'user_id' => $user->id,
          'name' => 'アップルウォッチ',
          'image' => 'apple.jpg',
          ]);
        $matchedItem->categories()->attach($categories->pluck('id'));

        // 検索にヒットしない商品
        $unmatchedItem = Item::factory()->create([
          'user_id' => $user->id,
          'name' => 'バナナケース',
          'image' => 'banana.jpg',
          ]);
        $unmatchedItem->categories()->attach($categories->pluck('id'));

        // 検索キーワードを指定してトップページにアクセス
        $response = $this->get(route('top', ['keyword' => 'アップル']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 部分一致する商品が表示されていること
        $response->assertSee($matchedItem->name);
        $response->assertSee(Storage::url($matchedItem->image));

        // 一致しない商品は表示されないこと
        $response->assertDontSee($unmatchedItem->name);
        $response->assertDontSee(Storage::url($unmatchedItem->image));
      }


      /** 検索状態がマイリストでも保持されている */
      /** @test */
      public function Top_page_displays_items_matching_keyword_partially_onmylist()
      {
        // ユーザーとカテゴリを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $categories = Category::factory()->count(2)->create();

        // 検索にヒットする商品（他人の商品）
        $likedItem = Item::factory()->create([
          'user_id' => $otherUser->id,
          'name' => 'アップルウォッチ',
          'image' => 'apple.jpg',
          ]);
        $likedItem->categories()->attach($categories->pluck('id'));

        // 検索にヒットしない商品
        $unmatchedItem = Item::factory()->create([
          'user_id' => $otherUser->id,
          'name' => 'バナナケース',
          'image' => 'banana.jpg',
          ]);
        $unmatchedItem->categories()->attach($categories->pluck('id'));

        // 検索キーワードを指定してトップページにアクセス
        $response = $this->get(route('top', ['keyword' => 'アップル']));

        // ステータスコード確認
        $response->assertStatus(200);

        // 部分一致する商品が表示されていること
        $response->assertSee($likedItem->name);
        $response->assertSee(Storage::url($likedItem->image));

        // 一致しない商品は表示されないこと
        $response->assertDontSee($unmatchedItem->name);
        $response->assertDontSee(Storage::url($unmatchedItem->image));

        // ログイン状態にする
        $this->actingAs($user);

        // いいね済みにする
        $user->likedItems()->attach($likedItem->id);

        // マイリストタブにアクセス
        $response = $this->get(route('top', ['tab' => 'mylist']));

        // 部分一致する商品が表示されていること
        $response->assertSee($likedItem->name);
        $response->assertSee(Storage::url($likedItem->image));
      }
  }