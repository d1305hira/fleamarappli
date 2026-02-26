@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 500px;">
    <h2 class="text-center mb-4">会員登録</h2>
      <div class="card-body">
        <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <!-- ユーザー名 -->
        <div class="form-group mb-3">
          <label for="name">ユーザー名</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
        </div>

        <!-- メールアドレス -->
        <div class="form-group mb-3">
          <label for="email">メールアドレス</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" >
              @error('email')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
        </div>

        <!-- パスワード -->
        <div class="form-group mb-3">
            <label for="password">パスワード</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                  name="password" >
            @error('password')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- パスワード（確認） -->
        <div class="form-group mb-4">
            <label for="password-confirm">確認用パスワード</label>
            <input id="password-confirm" type="password" class="form-control"
                  name="password_confirmation" >
        </div>

        <!-- 登録ボタン -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-danger">登録する</button>
        </div>

        <!-- ログインリンク -->
        <div class="text-center">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </div>
        </form>
      </div>
</div>
@endsection
