@extends('layouts.app')

@section('content')

{{-- ヘッダー右側を非表示 --}}
<style>
    .header nav,
    .header .header-right,
    .header form {
        display: none !important;
    }
</style>

<div class="verify-email-wrapper" style="
    max-width: 600px;
    margin: 140px auto;
    text-align: center;
">

    <p style="margin-bottom: 40px; line-height: 1.8;">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- ▼ 未認証：MailHog を開く --}}
    @if (auth()->user()->email_verified_at === null)
    <a href="http://localhost:8025"
        target="_blank"
        style="
                display: inline-block;
                padding: 12px 40px;
                background-color: #ff4d4f;
                color: #fff;
                border-radius: 6px;
                font-size: 16px;
                text-decoration: none;
            ">
        認証メールを確認する
    </a>
    @else
    {{-- ▼ 認証済み：プロフィール設定へ --}}
    <a href="{{ route('profile.setup') }}"
        style="
                display: inline-block;
                padding: 12px 40px;
                background-color: #1e6bb8;
                color: #fff;
                border-radius: 6px;
                font-size: 16px;
                text-decoration: none;
            ">
        プロフィール設定へ進む
    </a>
    @endif

    @if (session('status') === 'verification-link-sent')
    <p style="color: green; margin-top: 20px;">
        認証メールを再送しました。
    </p>
    @endif

    <div style="margin-top: 30px;">
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
    </div>

</div>
@endsection