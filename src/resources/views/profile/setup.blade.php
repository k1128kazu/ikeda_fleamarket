@extends('layouts.app')

@section('content')
<div class="profile-container">

    <h2 class="profile-title">プロフィール設定</h2>

    <form method="POST"
        action="{{ route('profile.storeInitial') }}"
        enctype="multipart/form-data"
        novalidate>
        @csrf

        {{-- プロフィール画像 --}}
        <div class="profile-image-area">
            <img
                id="profile_preview"
                src="{{ asset('storage/user/user_default.png') }}"
                alt="プロフィール画像"
                class="profile-circle">

            <label class="image-select-btn">
                画像を選択する
                <input
                    type="file"
                    name="image"
                    id="profile_image"
                    accept="image/*"
                    hidden>
            </label>

            @error('image')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ユーザー名 --}}
        <div class="profile-group">
            <label>ユーザー名</label>
            <input type="text"
                name="name"
                value="{{ old('name', $user->name) }}">
            @error('name')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="profile-group">
            <label>郵便番号</label>
            <input type="text"
                name="postcode"
                value="{{ old('postcode', $user->postcode) }}">
            @error('postcode')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="profile-group">
            <label>住所</label>
            <input type="text"
                name="address"
                value="{{ old('address', $user->address) }}">
            @error('address')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="profile-group">
            <label>建物名</label>
            <input type="text"
                name="building"
                value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="profile-btn">
            更新する
        </button>

    </form>
</div>

{{-- 画像プレビュー --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('profile_image');
        const preview = document.getElementById('profile_preview');

        if (!input || !preview) return;

        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            preview.src = URL.createObjectURL(file);
        });
    });
</script>
@endsection