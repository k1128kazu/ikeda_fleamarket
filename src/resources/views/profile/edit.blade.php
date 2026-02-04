@extends('layouts.app')

@section('content')

<div class="profile-edit-page">

    <h2 class="profile-edit-title">プロフィール設定</h2>

    <form method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="profile-edit-form"
        novalidate>
        @csrf
        @method('PUT')

        {{-- 画像 --}}
        <div class="profile-edit-image-row">
            <div class="profile-edit-avatar">
                <img
                    id="profile_preview"
                    src="{{ $user->image
                        ? asset('storage/' . $user->image)
                        : asset('storage/material/user_default.png') }}"
                    alt="ユーザー画像"
                    class="profile-edit-avatar-img">
            </div>

            <label class="profile-edit-image-btn">
                画像を選択する
                <input
                    type="file"
                    name="profile_image"
                    id="profile_image"
                    class="profile-edit-file"
                    accept="image/*">
            </label>
        </div>

        @error('profile_image')
        <p class="form-error">{{ $message }}</p>
        @enderror

        {{-- ユーザー名 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">ユーザー名</label>
            <input type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="profile-edit-input">
            @error('name')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">郵便番号</label>
            <input type="text"
                name="postal_code"
                value="{{ old('postal_code', $user->postal_code) }}"
                class="profile-edit-input">
            @error('postal_code')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">住所</label>
            <input type="text"
                name="address01"
                value="{{ old('address01', $user->address01) }}"
                class="profile-edit-input">
            @error('address01')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">建物名</label>
            <input type="text"
                name="address02"
                value="{{ old('address02', $user->address02) }}"
                class="profile-edit-input">
        </div>

        {{-- 更新 --}}
        <button type="submit" class="profile-edit-submit">
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