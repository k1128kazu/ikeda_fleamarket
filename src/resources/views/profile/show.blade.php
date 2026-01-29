@extends('layouts.app')

@section('content')

<div class="profile-page">

    {{-- プロフィール上部 --}}
    <div class="profile-header">
        <div class="profile-left">
            <img
                src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('storage/material/user_default.png') }}"
                class="profile-image">
        </div>

        <div class="profile-center">
            <p class="profile-name">{{ auth()->user()->name }}</p>
        </div>

        <div class="profile-right">
            <a href="{{ route('profile.edit') }}" class="profile-edit-btn">
                プロフィールを編集
            </a>
        </div>
    </div>

    {{-- タブ --}}
    <div class="profile-tabs">
        <a href="{{ route('profile.show', ['tab' => 'sell']) }}"
            class="profile-tab {{ request('tab', 'sell') === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('profile.show', ['tab' => 'buy']) }}"
            class="profile-tab {{ request('tab') === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- ★ タブと商品一覧の間のライン --}}
    <div class="profile-divider"></div>

    {{-- 商品一覧 --}}
    <div class="profile-items">
        @forelse ($items as $item)
        <div class="profile-item">
            <img src="{{ asset('storage/' . $item->image_path) }}" class="profile-item-image">
            <p class="profile-item-name">{{ $item->name }}</p>
            <p class="profile-item-price">¥{{ number_format($item->price) }}</p>

            @if ($item->is_sold)
            <p class="profile-item-sold">SOLD</p>
            @endif
        </div>
        @empty
        <p class="profile-empty">商品はありません</p>
        @endforelse
    </div>

</div>

@endsection