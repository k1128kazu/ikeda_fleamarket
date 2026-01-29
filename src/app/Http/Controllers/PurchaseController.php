<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    /**
     * 購入画面表示
     */
    public function show(Item $item)
    {
        return view('purchases.show', compact('item'));
    }

    /**
     * 住所変更画面
     */
    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('purchases.address', compact('item', 'user'));
    }

    /**
     * 住所変更（セッション保存）
     */
    public function updateAddress(Request $request, Item $item)
    {
        $request->validate([
            'postcode' => 'required',
            'address'  => 'required',
        ]);

        session([
            'purchase_address' => [
                'postcode' => $request->postcode,
                'address'  => $request->address,
                'building' => $request->building,
            ]
        ]);

        return redirect()->route('purchase.show', $item);
    }

    /**
     * 購入確定（Stripe決済 → 成功後DB反映）
     */
    public function store(Request $request, Item $item)
    {
        // Stripeキー設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // Stripe Checkout セッション作成
        $session = StripeSession::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            // ★ 成功後に session_id を持って戻す
            'success_url' => route('purchase.complete') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.show', $item),
        ]);

        // Stripe画面へリダイレクト
        return redirect($session->url);
    }

    /**
     * 購入完了画面（★ここでDB反映）
     */
    public function complete(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            abort(400, 'session_id がありません');
        }

        // Stripe セッション取得
        $session = StripeSession::retrieve($sessionId);

        // すでに保存済みなら何もしない（二重防止）
        if (Purchase::where('stripe_session_id', $sessionId)->exists()) {
            return view('purchases.complete');
        }

        // 商品取得（metadataを使わず、金額一致前提）
        $item = Item::where('price', $session->amount_total)->firstOrFail();

        // 住所（セッション or ユーザー）
        $address = session('purchase_address');

        // purchases 保存
        Purchase::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item->id,
            'postcode'       => $address['postcode'] ?? Auth::user()->postcode,
            'address'        => $address['address'] ?? Auth::user()->address,
            'building'       => $address['building'] ?? Auth::user()->building,
            'payment_method' => $session->payment_method_types[0],
            'stripe_session_id' => $sessionId,
        ]);

        // 商品を SOLD にする
        $item->update([
            'is_sold' => true,
        ]);

        // セッション削除
        session()->forget('purchase_address');

        return view('purchases.complete');
    }
}
