<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    /**
     * マイページ表示
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        // 出品した商品
        $sellItems = Item::where('user_id', $user->id)
            ->latest()
            ->get();

        // 購入した商品
        $buyItemIds = Purchase::where('user_id', $user->id)
            ->pluck('item_id');

        $buyItems = Item::whereIn('id', $buyItemIds)
            ->latest()
            ->get();

        // タブ制御（初期は出品）
        $tab = $request->query('tab', 'sell');

        $items = $tab === 'buy' ? $buyItems : $sellItems;

        return view('profile.show', compact(
            'user',
            'items',
            'sellItems',
            'buyItems',
            'tab'
        ));
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * プロフィール更新処理（★ 日本語バリデーション対応）
     */
    public function update(ProfileRequest $request)
    {
        // ProfileRequest の rules / messages がここで有効
        $validated = $request->validated();

        $user = Auth::user();

        // 画像アップロード
        if ($request->hasFile('image')) {

            // 既存画像削除（あれば）
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $path = $request->file('image')->store('users', 'public');
            $validated['image'] = $path;
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'プロフィールを更新しました');
    }
}
