<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録ページが正常に表示されることを確認する
     */
    public function test_名前未入力で登録するとバリデーションエラーになる()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. 名前を入力せず、他の必要項目を入力
        $formData = [
          // 'name' => '', // intentionally omitted
          'email' => 'user@example.com',
          'password' => 'securePassword123',
          'password_confirmation' => 'securePassword123',
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // バリデーションエラーを確認
        $postResponse->assertSessionHasErrors(['name']);

        // エラーメッセージが表示されているか確認
        $followed = $this->followRedirects($postResponse);
        $followed->assertSee('お名前を入力してください');
      }

		public function test_メールアドレスが入力されていない場合、バリデーションエラーになる()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. メールアドレスを入力せず、他の必要項目を入力
        $formData = [
          'name' => 'テストユーザー',
          // 'email' => '', // intentionally omitted
          'password' => 'securePassword123',
          'password_confirmation' => 'securePassword123',
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // バリデーションエラーを確認
        $postResponse->assertSessionHasErrors(['email']);

        // エラーメッセージが表示されているか確認
        $followed = $this->followRedirects($postResponse);
        $followed->assertSee('メールアドレスを入力してください');
      }


		public function test_パスワードが入力されていない場合、バリデーションエラーになる()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. パスワードを入力せず、他の必要項目を入力
        $formData = [
          'name' => 'テストユーザー',
          'email' => 'user@example.com',
				  // 'password' => '', // intentionally omitted
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // バリデーションエラーを確認
        $postResponse->assertSessionHasErrors(['password']);

        // エラーメッセージが表示されているか確認
        $followed = $this->followRedirects($postResponse);
        $followed->assertSee('パスワードを入力してください');
      }


		public function test_パスワードが7文字以下の場合、バリデーションエラーになる()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. パスワードを７文字以下で入力し、他の必要項目を入力
        $formData = [
          'name' => 'テストユーザー',
          'email' => 'user@example.com',
          'password' => 'short7',// 7文字以下
          'password_confirmation' => 'short7',
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // バリデーションエラーを確認
        $postResponse->assertSessionHasErrors(['password']);

        // エラーメッセージが表示されているか確認
        $followed = $this->followRedirects($postResponse);
        $followed->assertSee('パスワードは8文字以上で入力してください');
      }


		public function test_パスワードが確認用と一致しない場合、バリデーションエラーになる()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. パスワードと確認用パスワードが一致しないデータを用意
        $formData = [
          'name' => 'テストユーザー',
          'email' => 'user@example.com',
          'password' => 'securePassword123',
          'password_confirmation' => 'differentPassword123',
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // バリデーションエラーを確認
        $postResponse->assertSessionHasErrors(['password']);

        // エラーメッセージが表示されているか確認
        $followed = $this->followRedirects($postResponse);
        $followed->assertSee('パスワードと一致しません');
      }

		public function test_全ての項目が正しく入力された場合は会員登録が成功しプロフィール設定画面に遷移する()
      {
        // 1. 会員登録ページを開く
        $getResponse = $this->get('/register');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('会員登録');

        // 2. 正常な入力データを用意
        $formData = [
          'name' => 'テストユーザー',
          'email' => 'user@example.com',
          'password' => 'securePassword123',
          'password_confirmation' => 'securePassword123',
          ];

        // 3. 登録ボタンを押す（POST送信）
        $postResponse = $this->post('/register', $formData);

        // 4. 登録後にプロフィール設定画面にリダイレクトされることを確認
        $postResponse->assertRedirect(route('profile.edit'));

        // 5. 実際にユーザーがDBに登録されていることを確認
        $this->assertDatabaseHas('users', [
          'name' => 'テストユーザー',
          'email' => 'user@example.com',
          ]);
      }
}