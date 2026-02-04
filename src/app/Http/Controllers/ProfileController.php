<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ProfileController extends Controller
{
    /**
     * マイページ表示
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        // ★ これを追加（最重要）
        if ($user->email_verified_at && empty($user->postcode)) {
            return redirect()->route('profile.setup');
        }
        // 出品した商品
        $sellItems = Item::where('user_id', $user->id)
            ->latest()
            ->get();

        // 購入した商品
        $buyItems = Item::whereHas('purchase', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->get();

        $items = $request->get('tab') === 'buy'
            ? $buyItems
            : $sellItems;

        return view('profile.show', [
            'user'  => $user,
            'items' => $items,
        ]);
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * プロフィール更新
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('user', 'public');
            $user->image = $path;
        }

        $user->name     = $request->name;
        $user->postcode = $request->postcode;
        $user->address  = $request->address;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('profile.show');
    }

    /**
     * 🔴 初回プロフィール設定画面（← これが無かった）
     */
    public function setup()
    {
        return view('profile.setup', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * 初回プロフィール保存
     */
    public function storeInitial(Request $request)
    {
        $request->validate(
            [
                'name'        => ['required'],
                'postal_code' => ['required'],
                'address01'   => ['required'],
            ],
            [
                'name.required'        => 'ユーザー名を入力してください',
                'postal_code.required' => '郵便番号を入力してください',
                'address01.required'   => '住所を入力してください',
            ]
        );
        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('user', 'public');
            $user->image = $path;
        }

        // ★★★ ここが最重要 ★★★
        $user->name     = $request->name;
        $user->postcode = $request->postal_code;
        $user->address  = $request->address01;
        $user->building = $request->address02;

        $user->save();

        return redirect()->route('profile.show');    }
}
