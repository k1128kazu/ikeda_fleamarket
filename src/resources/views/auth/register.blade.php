@extends('layouts.app')

@section('content')
<div class="login-container">

    <h2 class="login-title">会員登録</h2>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="login-group">
            <label>名前</label>
            <input type="text" name="name" class="login-input" value="{{ old('name') }}">
            @error('name')
            <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login-group">
            <label>メールアドレス</label>
            <input type="email" name="email" class="login-input" value="{{ old('email') }}">
            @error('email')
            <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login-group">
            <label>パスワード</label>
            <input type="password" name="password" class="login-input">
            @error('password')
            <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login-group">
            <label>パスワード（確認）</label>
            <input type="password" name="password_confirmation" class="login-input">
        </div>

        <button type="submit" class="login-btn">登録する</button>
    </form>

    <div class="register-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>

</div>
@endsection