@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 500px;">
  <h2 class="text-center mb-4">ログイン</h2>

  <div class="card-body">
    <form method="POST" action="{{ route('login') }}">
    @csrf
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

    <!-- ログインする -->
    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-danger">ログインする</button>
    </div>

    <!-- ログインリンク -->
    <div class="text-center">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
  </form>
  </div>
</div>
@endsection
