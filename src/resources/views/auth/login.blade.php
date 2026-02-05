@extends('layouts.app')

@section('content')
<div class="login-container">

    <h2 class="login-title">ログイン</h2>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="login-group">
            <label>メールアドレス</label>
            <input type="email" name="email" class="login-input" value="{{ old('email') }}">

            {{-- default エラーバッグ --}}
            @error('email')
            <p class="login-error">{{ $message }}</p>
            @enderror

            {{-- login エラーバッグ（Fortify対策） --}}
            @if ($errors->login->has('email'))
            <p class="login-error">{{ $errors->login->first('email') }}</p>
            @endif
        </div>

        <div class="login-group">
            <label>パスワード</label>
            <input type="password" name="password" class="login-input">

            @error('password')
            <p class="login-error">{{ $message }}</p>
            @enderror

            @if ($errors->login->has('password'))
            <p class="login-error">{{ $errors->login->first('password') }}</p>
            @endif
        </div>

        <button type="submit" class="login-btn">ログインする</button>
    </form>

    <div class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>

</div>
@endsection