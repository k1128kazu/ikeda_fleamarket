@extends('layouts.app')

@section('content')
<div style="max-width:600px; margin:60px auto;">

    <h2 style="text-align:center; font-size:20px; font-weight:bold; margin-bottom:30px;">
        住所の変更
    </h2>
    <form method="POST"
        action="{{ route('purchase.address.update', $item) }}"
        novalidate>
        @csrf
        @method('PUT')

        <div style="margin-bottom:20px;">
            <label>郵便番号</label>
            <input type="text"
                name="postcode"
                value="{{ old('postcode', $address['postcode'] ?? '') }}"
                style="width:100%; padding:10px;">
            @error('postcode')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom:20px;">
            <label>住所</label>
            <input type="text"
                name="address"
                value="{{ old('address', $address['address'] ?? '') }}"
                style="width:100%; padding:10px;">
            @error('address')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom:30px;">
            <label>建物名</label>
            <input type="text"
                name="building"
                value="{{ old('building', $address['building'] ?? '') }}"
                style="width:100%; padding:10px;">
        </div>

        <button type="submit"
            style="width:100%; background:#ff5a5a; color:#fff; border:none; padding:14px; font-size:16px;">
            更新する
        </button>
    </form>
</div>
@endsection