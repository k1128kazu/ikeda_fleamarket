@extends('layouts.app')

@section('content')
<div class="profile-container">

    <h2 class="profile-title">プロフィール設定</h2>

    <form method="POST" action="{{ route('profile.storeInitial') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 画像エリア -->
        <div class="profile-image-area">
            <img id="preview"
                src="{{ auth()->user()->image
        ? asset('storage/' . auth()->user()->image)
        : asset('storage/user/default.png') }}"
                class="profile-circle">

            <label class="image-select-btn">
                画像を選択する
                <input type="file" name="image" hidden> </label>

            @error('image')
            <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="profile-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}">
        </div>
        @error('name')
        <p class="login-error">{{ $message }}</p>
        @enderror

        <div class="profile-group">
            <label>郵便番号</label>
            <input type="text" name="postcode"
                value="{{ old('postcode', auth()->user()->postcode) }}">
        </div>
        @error('postcode')
        <p class="login-error">{{ $message }}</p>
        @enderror

        <div class="profile-group">
            <label>住所</label>
            <input type="text" name="address"
                value="{{ old('address', auth()->user()->address) }}">
        </div>
        @error('address')
        <p class="login-error">{{ $message }}</p>
        @enderror

        <div class="profile-group">
            <label>建物名</label>
            <input type="text" name="building"
                value="{{ old('building', auth()->user()->building) }}">
        </div>

        <button type="submit" class="profile-btn">更新する</button>
    </form>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[type="file"][name="image"]');
        const previewImg = document.getElementById('preview');

        if (!fileInput || !previewImg) return;

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
</script>



@endsection