@extends('layouts.app')

@section('content')

<div class="tab-menu">
    <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
        class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">
        おすすめ
    </a>

    <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
        class="{{ request('tab') === 'mylist' ? 'active' : '' }}">
        マイリスト
    </a>
</div>

<div class="item-index">
    <div class="item-grid">
        @forelse ($items as $item)
        <div class="item-card">
            <a href="{{ route('items.show', $item) }}">
                <div class="item-image-wrapper">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">

                    {{-- ★ SOLD 表示 --}}
                    @if($item->is_sold)
                    <span class="sold-label">SOLD</span>
                    @endif
                </div>

                <p class="item-name">{{ $item->name }}</p>
            </a>
        </div>
        @empty
        <p class="no-item">該当する商品がありません</p>
        @endforelse
    </div>
</div>

@endsection