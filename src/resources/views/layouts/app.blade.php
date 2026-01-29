<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>COACHTECH フリマ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <header class="header">
        <div class="header-inner">

            {{-- ロゴ（左） --}}
            <div class="header-left">
                <a href="{{ route('items.index') }}">
                    <img src="{{ asset('storage/material/COACHTECHヘッダーロゴ.png') }}"
                        alt="COACHTECH"
                        class="header-logo">
                </a>
            </div>

            {{-- 🔍 検索（中央・CSS追加なし） --}}
            <div class="header-search">
                <form action="{{ route('items.index') }}" method="GET">
                    <input
                        type="text"
                        name="keyword"
                        placeholder="なにをお探しですか？"
                        value="{{ request('keyword') }}">
                </form>
            </div>

            {{-- 右リンク --}}
            <div class="header-right">

                @guest
                <a href="{{ route('login') }}" class="header-link">ログイン</a>
                <a href="{{ route('login') }}" class="header-link">マイページ</a>
                <a href="{{ route('login') }}" class="sell-btn">出品</a>
                @endguest

                @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="header-link logout-btn">
                        ログアウト
                    </button>
                </form>

                <a href="{{ route('profile.show') }}" class="header-link">マイページ</a>
                <a href="{{ route('items.create') }}" class="sell-btn">出品</a>
                @endauth

            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>