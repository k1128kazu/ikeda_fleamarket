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
                    src="{{ $user->image ? asset('storage/' . $user->image) : asset('storage/material/user_default.png') }}"
                    alt="ユーザー画像"
                    class="profile-edit-avatar-img">
            </div>

            <label class="profile-edit-image-btn">
                画像を選択する
                <input type="file" name="image" class="profile-edit-file">
            </label>
        </div>

        @error('image')
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
                name="postcode"
                value="{{ old('postcode', $user->postcode) }}"
                class="profile-edit-input">
            @error('postcode')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">住所</label>
            <input type="text"
                name="address"
                value="{{ old('address', $user->address) }}"
                class="profile-edit-input">
            @error('address')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="profile-edit-group">
            <label class="profile-edit-label">建物名</label>
            <input type="text"
                name="building"
                value="{{ old('building', $user->building) }}"
                class="profile-edit-input">
            @error('building')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 更新 --}}
        <button type="submit" class="profile-edit-submit">
            更新する
        </button>

    </form>
</div>

@endsection