@extends('layouts.app')

@section('content')

<div class="purchase-page">

    <div class="purchase-main">

        {{-- ================= 左エリア ================= --}}
        <div class="purchase-left">

            {{-- 商品情報 --}}
            <div class="purchase-item">
                <img src="{{ asset('storage/' . $item->image_path) }}" class="purchase-image">
                <div class="purchase-info">
                    <p class="purchase-name">{{ $item->name }}</p>
                    <p class="purchase-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr>

            {{-- ★ ここから form 開始（重要） --}}
            <form method="POST" action="{{ route('purchase.store', $item) }}" novalidate>
                @csrf

                {{-- 支払い方法（★ form の中に入れる） --}}
                <div class="purchase-block">
                    <p class="purchase-label">支払い方法</p>
                    <select name="payment_method" id="payment_method" class="purchase-select">
                        <option value="card" selected>クレジットカード</option>
                        <option value="konbini">コンビニ払い</option>
                    </select>
                </div>

                <hr>

                {{-- 配送先 --}}
                <div class="purchase-block">
                    <div class="purchase-address-head">
                        <p class="purchase-label">配送先</p>
                        <a href="{{ route('purchase.address.edit', $item) }}" class="purchase-change">
                            変更する
                        </a>
                    </div>

                    <p>〒{{ session('purchase_address.postcode') ?? auth()->user()->postcode }}</p>
                    <p>
                        {{ session('purchase_address.address') ?? auth()->user()->address }}
                        {{ session('purchase_address.building') ?? auth()->user()->building }}
                    </p>
                </div>

        </div>

        {{-- ================= 右エリア ================= --}}
        <div class="purchase-right">

            <div class="purchase-summary">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>
                <div class="summary-row">
                    <span>支払い方法</span>
                    <span id="summary-payment">クレジットカード</span>
                </div>
            </div>

            <button type="submit" class="purchase-btn">
                購入する
            </button>

        </div>

        </form>
        {{-- ★ ここで form 終了 --}}

    </div>
</div>

{{-- 支払い方法 表示切替（見た目用） --}}
<script>
    const select = document.getElementById('payment_method');
    const summary = document.getElementById('summary-payment');

    select.addEventListener('change', function() {
        summary.textContent =
            this.value === 'card' ? 'クレジットカード' : 'コンビニ払い';
    });
</script>

@endsection