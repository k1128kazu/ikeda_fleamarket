@extends('layouts.app')

@section('content')

<style>
    .header nav,
    .header .header-right,
    .header form {
        display: none !important;
    }
</style>

<div style="max-width: 600px; margin: 140px auto; text-align: center;">

    <p style="margin-bottom: 40px; line-height: 1.8;">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <a href="http://localhost:8025"
        target="_blank"
        style="
        display: inline-block;
        padding: 16px 48px;
        background-color: #e0e0e0;
        color: #000;
        border-radius: 12px;
        font-size: 18px;
        font-weight: bold;
        text-decoration: none;
        margin-bottom: 32px; 
   ">
        認証はこちらから
    </a>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" style="
            background: none;
            border: none;
            color: #1e6bb8;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        ">
            認証メールを再送する
        </button>
    </form>

    @if (session('status') === 'verification-link-sent')
    <p style="color: green; margin-top: 20px;">
        認証メールを再送しました。
    </p>
    @endif

</div>
@endsection