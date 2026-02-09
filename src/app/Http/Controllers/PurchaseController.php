<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\UpdateShippingAddressRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    /**
     * 購入画面
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
     * 住所変更（一時保存）
     */
    public function updateAddress(UpdateShippingAddressRequest $request, Item $item)
    {
        session([
            'shipping.postcode' => $request->postcode,
            'shipping.address'  => $request->address,
            'shipping.building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $item);
    }

    /**
     * 購入処理
     * ・クレジット／コンビニ共通で Stripe に遷移
     * ・コンビニ払いのみ SOLD を先に付ける
     */
    public function store(PurchaseRequest $request, Item $item)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card', 'konbini'],
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 3,
                ],
            ],
            'customer_email' => Auth::user()->email,
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
            'metadata' => [
                'item_id' => (string) $item->id,
                'buyer_id' => (string) Auth::id(),
                'selected_payment_method' => (string) $request->payment_method,
            ],
            'success_url' => route('purchase.complete') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.show', $item),
        ]);

        // 購入履歴はここで必ず作る（マーキング）
        Purchase::unguarded(function () use ($item, $session, $request) {
            Purchase::updateOrCreate(
                ['item_id' => $item->id],
                [
                    'user_id' => Auth::id(),
                    'postcode' => Auth::user()->postcode,
                    'address'  => Auth::user()->address,
                    'building' => Auth::user()->building,
                    'payment_method' => $request->payment_method,
                    'stripe_session_id' => $session->id,
                ]
            );
        });

        // コンビニ払いは申込時点で SOLD
        if ($request->payment_method === 'konbini') {
            $item->update(['is_sold' => true]);
        }

        // ★ クレジット／コンビニ共通で Stripe へ
        return redirect($session->url);
    }

    /**
     * 購入完了
     * ・クレジットカード決済後に SOLD を付ける
     */
    public function complete()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = request('session_id');
        if (!$sessionId) {
            abort(400, 'session_id がありません');
        }

        $session = StripeSession::retrieve($sessionId);

        $itemId = data_get($session, 'metadata.item_id');
        if (!$itemId) {
            abort(400, 'item_id が取得できません');
        }

        $item = Item::findOrFail($itemId);

        // クレジットカード決済後に SOLD
        if (!$item->is_sold) {
            $item->update(['is_sold' => true]);
        }

        session()->forget('shipping');

        return view('purchases.complete');
    }
}
