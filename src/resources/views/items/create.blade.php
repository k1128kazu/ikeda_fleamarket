@extends('layouts.app')

@section('content')
<div class="sell-container">

    <h2 class="sell-title">商品の出品</h2>

    <form action="{{ route('items.store') }}"
        method="POST"
        enctype="multipart/form-data"
        novalidate>
        @csrf

        {{-- 商品画像 --}}
        <div class="sell-section">
            <label class="sell-label">商品画像</label>

            <div class="image-upload-area" style="text-align:center;">
                <label for="image" class="image-select-btn">
                    画像を選択する
                </label>
                <input type="file" id="image" name="image" accept="image/*" hidden>

                {{-- プレビュー --}}
                <div style="margin-top:20px;">
                    <img
                        id="preview"
                        src=""
                        alt="プレビュー"
                        style="
                            display:none;
                            max-width:100%;
                            max-height:300px;
                            object-fit:contain;
                            margin:0 auto;
                        ">
                </div>
            </div>

            @error('image')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品の詳細 --}}
        <div class="sell-section">
            <h3 class="sell-subtitle">商品の詳細</h3>

            {{-- カテゴリー --}}
            <label class="sell-label">カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                <label class="category-item">
                    <input
                        type="checkbox"
                        name="categories[]"
                        value="{{ $category->id }}"
                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                    <span>{{ $category->name }}</span>
                </label>
                @endforeach
            </div>

            @error('categories')
            <p class="error">{{ $message }}</p>
            @enderror


            @error('categories')
            <p class="error">{{ $message }}</p>
            @enderror

            {{-- 商品の状態 --}}
            <label class="sell-label">商品の状態</label>
            <select name="status" class="sell-select">
                <option value="">選択してください</option>
                <option value="良好" {{ old('status') === '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('status') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('status') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
            </select>

            @error('status')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品名と説明 --}}
        <div class="sell-section">
            <h3 class="sell-subtitle">商品名と説明</h3>

            <label class="sell-label">商品名</label>
            <input
                type="text"
                name="name"
                class="sell-input"
                value="{{ old('name') }}">

            @error('name')
            <p class="error">{{ $message }}</p>
            @enderror

            <label class="sell-label">商品の説明</label>
            <textarea
                name="description"
                class="sell-textarea">{{ old('description') }}</textarea>

            @error('description')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 販売価格 --}}
        <div class="sell-section">
            <label class="sell-label">販売価格</label>
            <div class="price-input">
                <span>¥</span>
                <input
                    type="text"
                    name="price"
                    value="{{ old('price') }}">
            </div>

            @error('price')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell-submit-btn">
            出品する
        </button>

    </form>
</div>

{{-- 画像プレビュー用JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('image');
        const preview = document.getElementById('preview');

        if (!input || !preview) return;

        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        });
    });
</script>
@endsection