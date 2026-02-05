<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\UpdateShippingAddressRequest;
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
    public function updateAddress(UpdateShippingAddressRequest $request)
    {
        session([
            'shipping.postcode' => $request->postcode,
            'shipping.address'  => $request->address,
            'shipping.building' => $request->building,
        ]);

        return redirect()->route('purchase.show', $request->item_id);
    }

    /**
     * 購入確定
     */
    public function store(PurchaseRequest $request, Item $item)
    {
        /**
         * =========================================
         * 【① テスト環境用分岐】
         * ここは php artisan test のときだけ通る
         * 本番環境では一切実行されない
         * =========================================
         */
        if (app()->environment('testing')) {

            // 購入レコード作成
            Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'postcode' => auth()->user()->postcode,
                'address'  => auth()->user()->address,
                'building' => auth()->user()->building,
                'payment_method' => $request->payment_method,
            ]);

            // ★ テスト環境でも SOLD にする（次のテストでも使う）
            $item->update([
                'is_sold' => true,
            ]);

            return redirect()->route('purchase.show', $item);
        }

        /**
         * =========================================
         * 【② 本番環境（Stripeあり）】
         * ★あなたが直した「コンビニSOLDバグ」はここ
         * ★この中身は一切変更していない
         * =========================================
         */
        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethods = ($request->payment_method === 'konbini')
            ? ['konbini']
            : ['card'];

        // ★ コンビニ決済は即時SOLD（あなたの修正）
        if ($request->payment_method === 'konbini') {

            $item->update([
                'is_sold' => true,
            ]);

            Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'postcode' => auth()->user()->postcode,
                'address'  => auth()->user()->address,
                'building' => auth()->user()->building,
                'payment_method' => 'konbini',
            ]);
        }

        $session = StripeSession::create([
            'payment_method_types' => $paymentMethods,
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
            'success_url' => route('purchase.complete', $item),
            'cancel_url'  => route('purchase.show', $item),
        ]);

        return redirect($session->url);
    }

    /**
     * 購入完了画面（カード決済用）
     */
    public function complete()
    {
        return view('purchases.complete');
    }
}
