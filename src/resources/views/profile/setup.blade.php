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
                    name="profile_image"
                    id="profile_image"
                    accept="image/*"
                    hidden>
            </label>

            @error('profile_image')
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
                name="postal_code"
                value="{{ old('postal_code', $user->postal_code) }}">
            @error('postal_code')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="profile-group">
            <label>住所</label>
            <input type="text"
                name="address01"
                value="{{ old('address01', $user->address01) }}">
            @error('address01')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="profile-group">
            <label>建物名</label>
            <input type="text"
                name="address02"
                value="{{ old('address02', $user->address02) }}">
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