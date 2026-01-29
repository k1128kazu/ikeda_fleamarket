@extends('layouts.app')

@section('content')

<div style="max-width:600px; margin:80px auto; text-align:center;">

    <h2 style="font-size:22px; font-weight:bold; margin-bottom:30px;">
        購入が完了しました
    </h2>

    <p style="margin-bottom:40px;">
        ご購入ありがとうございます。
    </p>

    <a href="{{ route('items.index') }}"
        style="display:inline-block; padding:12px 30px; background:#ff5a5a; color:#fff; text-decoration:none;">
        トップへ戻る
    </a>

</div>

@endsection