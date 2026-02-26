<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
  {
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_メールアドレス未入力でバリデーションエラーになる()
      {
        $response = $this->from('/login')->post(route('login'), [
          'email' => '',
          'password' => 'securePassword123',
          ]);

        // ログイン画面にリダイレクトされる
        $response->assertRedirect('/login');

        // セッションにバリデーションエラーが含まれている
        $response->assertSessionHasErrors(['email']);

        // 認証されていないことを確認
        $this->assertGuest();
      }

    public function test_パスワード未入力でバリデーションエラーになる()
      {
        $response = $this->from('/login')->post(route('login'), [
          'email' => 'user@example.com',
          'password' => '',
          ]);

        // ログイン画面にリダイレクトされる
        $response->assertRedirect('/login');

        // セッションにバリデーションエラーが含まれている
        $response->assertSessionHasErrors(['password']);

        // 認証されていないことを確認
        $this->assertGuest();
      }

    public function test_登録されていない情報でログインすると認証エラーになる()
    {
        $response = $this->from('/login')->post(route('login'), [
          'email' => 'notfound@example.com',
          'password' => 'invalidPassword123',
          ]);

        // ログイン画面にリダイレクトされる
        $response->assertRedirect('/login');

        // セッションに認証エラーが含まれている（LoginRequestではなく、Auth::attempt()の失敗）
        $response->assertSessionHasErrors(['email']);

        // 認証されていないことを確認
        $this->assertGuest();
    }

    public function test_正しい情報が入力されたらログイン処理が実行される()
    {
      // ユーザー作成（DBに登録されているユーザー）
      $user = \App\Models\User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('securePassword123'),
        ]);

      // ログイン情報を送信
      $response = $this->post(route('login'), [
        'email' => 'user@example.com',
        'password' => 'securePassword123',
        ]);

      // ログイン成功後のリダイレクト先を確認（例：プロフィール編集画面）
      $response->assertRedirect(route('top'));

      // 認証されていることを確認
      $this->assertAuthenticatedAs($user);
    }

    public function test_ログアウトができる()
      {
        // ユーザー作成（DBに登録されているユーザー）
        $user = \App\Models\User::factory()->create([
          'email' => 'user@example.com',
          'password' => bcrypt('securePassword123'),
          ]);

        // ログイン情報を送信
        $response = $this->post(route('login'), [
          'email' => 'user@example.com',
          'password' => 'securePassword123',
          ]);

        // ログイン成功後のリダイレクト先を確認（例：プロフィール編集画面）
        $response->assertRedirect(route('top'));

        // 認証されていることを確認
        $this->assertAuthenticatedAs($user);

        // ログアウト処理
        $this->post('/logout');
      }
  }
