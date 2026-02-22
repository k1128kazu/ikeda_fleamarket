<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

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
    public function update(ProfileRequest $request)
    {

        $user = Auth::user();

        // 画像は「選ばれた時だけ」更新
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('user', 'public');
            $user->image = $path;   // ★ users.image に統一
        }

        // 通常項目
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
    public function storeInitial(ProfileRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('user', 'public');
            $user->image = $path;
        }

        // ★ フィールド名を ProfileRequest に合わせる
        $user->name     = $request->name;
        $user->postcode = $request->postcode;
        $user->address  = $request->address;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('profile.show');
    }}
