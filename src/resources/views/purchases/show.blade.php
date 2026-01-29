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

            {{-- 支払い方法（表示・選択UIは残す） --}}
            <div class="purchase-block">
                <p class="purchase-label">支払い方法</p>
                <select name="payment_method_display" id="payment_method" class="purchase-select">
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

            {{-- ★ Stripe 決済へ --}}
            <form method="POST" action="{{ route('purchase.store', $item) }}" novalidate>
                @csrf

                {{-- Stripe 用（最終的に送る値） --}}
                <input type="hidden" name="payment_method" id="payment_method_hidden" value="stripe">

                <button type="submit" class="purchase-btn">
                    購入する
                </button>
            </form>

        </div>

    </div>
</div>

{{-- 支払い方法 表示切替用（見た目だけ） --}}
<script>
    const select = document.getElementById('payment_method');
    const summary = document.getElementById('summary-payment');

    select.addEventListener('change', function() {
        summary.textContent =
            this.value === 'card' ? 'クレジットカード' : 'コンビニ払い';
    });
</script>

@endsection