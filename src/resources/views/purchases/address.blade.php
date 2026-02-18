@extends('layouts.app')

@section('content')

<div class="address-edit-wrapper">
    <h1 class="address-edit-title">住所の変更</h1>

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>郵便番号</label>
            <input
                type="text"
                name="postcode"
                class="address-input"
                value="{{ old('postcode', auth()->user()->postcode) }}">
        </div>

        <div class="form-group">
            <label>住所</label>
            <input
                type="text"
                name="address"
                class="address-input"
                value="{{ old('address', auth()->user()->address) }}">
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input
                type="text"
                name="building"
                class="address-input"
                value="{{ old('building', auth()->user()->building) }}">
        </div>

        <button type="submit" class="address-update-btn">
            更新する
        </button>
    </form>
</div>

@endsection