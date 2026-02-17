@extends('layouts.app')

@section('content')

@php
// ★ 再描画時も選択状態を維持する
$paymentMethod = old('payment_method', 'card');
@endphp

<div class="purchase-page">
    <div class="purchase-main">

        {{-- 左 --}}
        <div class="purchase-left">

            <div class="purchase-item">
                <img src="{{ asset('storage/' . $item->image_path) }}" class="purchase-image">
                <div class="purchase-info">
                    <p class="purchase-name">{{ $item->name }}</p>
                    <p class="purchase-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr>

            <form method="POST" action="{{ route('purchase.store', $item) }}" novalidate>
                @csrf

                {{-- 支払い方法 --}}
                <div class="purchase-block">
                    <p class="purchase-label">支払い方法</p>

                    <select name="payment_method" id="payment_method" class="purchase-select">
                        <option value="card" {{ $paymentMethod === 'card' ? 'selected' : '' }}>
                            クレジットカード
                        </option>
                        <option value="konbini" {{ $paymentMethod === 'konbini' ? 'selected' : '' }}>
                            コンビニ払い
                        </option>
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
                    @php
                    $ship = session('shipping');
                    @endphp

                    <p>〒{{ $ship['postcode'] ?? auth()->user()->postcode }}</p>
                    <p>
                        {{ $ship['address'] ?? auth()->user()->address }}
                        {{ $ship['building'] ?? auth()->user()->building }}
                    </p>


                </div>

        </div>

        {{-- 右 --}}
        <div class="purchase-right">

            <div class="purchase-summary">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                <div class="summary-row">
                    <span>支払い方法</span>
                    <span id="summary-payment">
                        {{ $paymentMethod === 'konbini' ? 'コンビニ払い' : 'クレジットカード' }}
                    </span>
                </div>
            </div>

            <button type="submit" class="purchase-btn">
                購入する
            </button>

        </div>

        </form>

    </div>
</div>

<script>
    const select = document.getElementById('payment_method');
    const summary = document.getElementById('summary-payment');

    if (select && summary) {
        select.addEventListener('change', function() {
            summary.textContent =
                this.value === 'konbini' ?
                'コンビニ払い' :
                'クレジットカード';
        });
    }
</script>

@endsection