@extends('layouts.app')

@section('content')

<div class="detail-wrapper">

    <!-- 左：商品画像 -->
    <div class="detail-left">
        <img src="{{ asset('storage/' . $item->image_path) }}"
            class="detail-img"
            alt="商品画像">
    </div>

    <!-- 右：商品情報 -->
    <div class="detail-right">

        <!-- 商品名 -->
        <h2 class="detail-title">{{ $item->name }}</h2>

        <!-- ブランド -->
        @if ($item->brand)
        <p class="detail-brand">{{ $item->brand }}</p>
        @endif

        <!-- 価格 -->
        <p class="detail-price">¥{{ number_format($item->price) }}</p>

        <!-- いいね・コメント -->
        <div class="detail-icons">

            <!-- いいね -->
            <div class="icon-box">
                @auth
                @if ($item->likes->where('user_id', auth()->id())->count() > 0)
                <!-- ON：ピンク -->
                <form method="POST" action="{{ route('like.destroy', $item->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border:none;background:none;cursor:pointer;">
                        <img src="{{ asset('storage/material/ハートロゴ_ピンク.png') }}">
                    </button>
                </form>
                @else
                <!-- OFF：グレー -->
                <form method="POST" action="{{ route('like.store', $item->id) }}">
                    @csrf
                    <button type="submit" style="border:none;background:none;cursor:pointer;">
                        <img src="{{ asset('storage/material/ハートロゴ_デフォルト.png') }}">
                    </button>
                </form>
                @endif
                @endauth

                @guest
                <a href="{{ route('login') }}">
                    <img src="{{ asset('storage/material/ハートロゴ_デフォルト.png') }}">
                </a>
                @endguest

                <span>{{ $item->likes->count() }}</span>
            </div>

            <!-- コメント -->
            <div class="icon-box">
                <img src="{{ asset('storage/material/ふきだしロゴ.png') }}">
                <span>{{ $item->comments->count() }}</span>
            </div>

        </div>

        <!-- 購入ボタン -->
        @auth
        @if(!$item->is_sold)
        <a href="{{ route('purchase.show', $item->id) }}" class="buy-btn">
            購入手続きへ
        </a>
        @else
        <button class="buy-btn" disabled
            style="background:#ccc; cursor:not-allowed;">
            購入済み
        </button>
        @endif
        @endauth

        @guest
        <a href="{{ route('login') }}" class="buy-btn">
            購入手続きへ
        </a>
        @endguest

        <!-- 商品説明 -->
        <p class="section-title">商品説明</p>
        <p>{{ $item->description }}</p>

        <!-- 商品の情報 -->
        <p class="section-title">商品の情報</p>

        <!-- 商品の状態（← ここが修正点） -->
        <p>商品の状態：{{ $item->condition }}</p>

        <!-- カテゴリー -->
        <p style="margin-top:10px;">カテゴリー：</p>
        @foreach ($item->categories as $category)
        <span class="category-tag">{{ $category->name }}</span>
        @endforeach

        <!-- コメント一覧 -->
        <p class="section-title">
            コメント（{{ $item->comments->count() }}）
        </p>

        @foreach ($item->comments as $comment)
        <div class="comment-box">
            <div class="comment-user">
                {{ $comment->user->name }}
            </div>
            <div>
                {{ $comment->content }}
            </div>
        </div>
        @endforeach

        <!-- コメント入力 -->
        @auth
        <p class="section-title">商品へのコメント</p>
        <form method="POST"
            action="{{ route('comment.store', $item) }}"
            novalidate>
            @csrf

            <textarea name="content"
                class="comment-input"></textarea>

            @error('content')
            <p class="login-error">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="comment-btn">
                コメントを送信する
            </button>
        </form>
        @endauth

    </div>
</div>

@endsection