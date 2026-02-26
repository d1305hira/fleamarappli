<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** ログイン済のユーザーはコメントを送信できる */
    /** @test */
    public function Logged_in_user_can_comment_on_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ① ログイン
        $this->actingAs($user);

        // ② 詳細ページを開く（オプション：表示確認）
        $this->get(route('item.show', $item->id))
            ->assertStatus(200)
            ->assertSee('コメント');

        // ③ コメントを投稿（POST送信）
        $this->post(route('comments.store', $item->id), [
            'comment' => 'テストコメント',
        ])->assertRedirect();

        // DBにコメントが保存されていることを確認
        $this->assertDatabaseHas('item_comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    /** ログイン前のユーザーはコメントを送信できない */
    /** @test */
    public function guest_user_cannot_comment_on_item()
    {
      $item = Item::factory()->create();

      // コメント投稿（未ログイン状態）
      $response = $this->post(route('comments.store', $item->id), [
        'comment' => 'テストコメント',
        ]);

      // ログインページにリダイレクトされることを確認
      $response->assertRedirect(route('login'));

      // DBに保存されていないことを確認（任意）
      $this->assertDatabaseMissing('item_comments', [
        'item_id' => $item->id,
        'comment' => 'テストコメント',
        ]);
    }

    /** コメントが入力されていない場合、バリデーションメッセージが表示される */
    /** @test */
    public function logged_in_user_cannot_submit_empty_comment()
    {
      $user = User::factory()->create();
      $item = Item::factory()->create();

      $this->actingAs($user);

      $response = $this->post(route('comments.store', $item->id), [
        'comment' => '', // 空コメント
        ]);

      // セッションにバリデーションエラーがあることを確認
      $response->assertSessionHasErrors('comment');

      // 元のページにリダイレクトされていること（通常は詳細ページ）
      $response->assertRedirect();

      // DBに保存されていないことを確認（任意）
      $this->assertDatabaseMissing('item_comments', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'comment' => '',
        ]);
    }


    /** コメントが255字以上の場合、バリデーションメッセージが表示される */
    /** @test */
    public function logged_in_user_cannot_submit_long_comment()
    {
      $user = User::factory()->create();
      $item = Item::factory()->create();

      $this->actingAs($user);

      // 256文字のコメントを生成
      $longComment = str_repeat('あ', 256);

      $response = $this->post(route('comments.store', $item->id), [
        'comment' => $longComment,
        ]);

      // バリデーションエラーが発生することを確認
      $response->assertSessionHasErrors('comment');

      // リダイレクトされることを確認（通常は元の詳細ページ）
      $response->assertRedirect();

      // DBに保存されていないことを確認（任意）
      $this->assertDatabaseMissing('item_comments', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'comment' => $longComment,
        ]);
    }
}