<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧（トップ）
     * ※ 既に実装済みならこのメソッドはそのまま or 削除OK
     */
    public function index(Request $request)
    {
        // タブ判定
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist' && Auth::check()) {
            // マイリスト（いいねした商品）
            $query = Item::with(['categories', 'likes'])
                ->whereHas('likes', function ($q) {
                    $q->where('user_id', Auth::id());
                });
        } else {
            // おすすめ（全商品：SOLD含む）
            $query = Item::with('categories');
        }

        // ★ キーワード検索（タブ共通）
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%");
            });
        }

        $items = $query->orderByDesc('created_at')->get();

        return view('items.index', compact('items', 'tab', 'keyword'));
    }
    /**
     * 商品詳細
     * ※ 既に実装済みならこのメソッドはそのまま or 削除OK
     */
    public function show(Item $item)
    {
        $item->load(['categories', 'likes', 'comments.user']);
        return view('items.show', compact('item'));
    }

    /**
     * 出品画面表示（/sell）
     */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /**
     * 出品処理（保存）
     */
    public function store(ExhibitionRequest $request)
    {
        // 画像保存（storage/app/public/items）
        $path = $request->file('image')->store('items', 'public');

        // 商品登録
        $item = Item::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'brand'       => $request->brand,   // ← 追加
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition, // ★ 統一
            'image_path'  => $path,
            'is_sold'     => false,
        ]);

        // カテゴリー中間テーブル登録
        // categories[] が必須＆ exists チェック済み（ExhibitionRequest）
        $item->categories()->sync($request->categories);

        // マイページへリダイレクト
        return redirect()->route('profile.show');
    }
}
