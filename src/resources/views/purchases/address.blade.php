@extends('layouts.app')

@section('content')

<div class="address-page">
    <h2>配送先の変更</h2>

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" novalidate>
        @csrf
        @method('PUT')

        <div>
            <label>郵便番号</label>
            <input type="text" name="postcode"
                value="{{ old('postcode', auth()->user()->postcode) }}">
        </div>

        <div>
            <label>住所</label>
            <input type="text" name="address"
                value="{{ old('address', auth()->user()->address) }}">
        </div>

        <div>
            <label>建物名</label>
            <input type="text" name="building"
                value="{{ old('building', auth()->user()->building) }}">
        </div>

        <button type="submit">更新する</button>
    </form>
</div>

@endsection