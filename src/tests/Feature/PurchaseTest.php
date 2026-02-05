<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーは購入画面にアクセスできない
     */
    public function test_guest_cannot_access_purchase_page()
    {
        // 出品者
        $seller = User::factory()->create();

        // 商品
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        // 未ログインで購入画面へアクセス
        $response = $this->get(route('purchase.show', $item->id));

        // ログイン画面へリダイレクトされる
        $response->assertRedirect(route('login'));
    }
    /**
     * ログイン済みユーザーは購入画面を表示できる
     */
    public function test_user_can_view_purchase_page()
    {
        // 出品者
        $seller = User::factory()->create();

        // 購入者
        $buyer = User::factory()->create();

        // 商品（未売却）
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        // 購入者としてログイン
        $this->actingAs($buyer);

        // 購入画面へアクセス
        $response = $this->get(route('purchase.show', $item->id));

        // ステータス200で表示される
        $response->assertStatus(200);

        // 商品名が表示されている（最低限の表示確認）
        $response->assertSee($item->name);
    }
    /**
     * 支払い方法が未選択の場合はバリデーションエラーになる
     */
    public function test_purchase_requires_payment_method()
    {
        // 出品者
        $seller = User::factory()->create();

        // 購入者
        $buyer = User::factory()->create();

        // 商品（未売却）
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        // 購入者としてログイン
        $this->actingAs($buyer);

        // 支払い方法を送らずに購入
        $response = $this->post(route('purchase.store', $item->id), [
            // 'payment_method' => 未送信
        ]);

        // 元の購入画面へリダイレクト
        $response->assertRedirect();

        // バリデーションエラーがある
        $response->assertSessionHasErrors([
            'payment_method',
        ]);

        // purchases テーブルにレコードは作成されていない
        $this->assertDatabaseCount('purchases', 0);

        // 商品は売却済みになっていない
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => false,
        ]);
    }
    /**
     * 購入が成功すると purchases テーブルにレコードが作成される
     */
    public function test_purchase_creates_purchase_record()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create([
            'postcode' => '123-4567',
            'address'  => '東京都渋谷区',
        ]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        $this->actingAs($buyer);

        $response = $this->post(route('purchase.store', $item->id), [
            'payment_method' => 'konbini',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'konbini',
        ]);
    }
    /**
     * 購入が成功すると商品が SOLD になる
     */
    public function test_purchase_marks_item_as_sold()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create([
            'postcode' => '123-4567',
            'address'  => '東京都渋谷区',
        ]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        $this->actingAs($buyer);

        $this->post(route('purchase.store', $item->id), [
            'payment_method' => 'konbini',
        ]);

        // 商品が SOLD になっていること
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }
    /**
     * 購入した商品がマイページの購入一覧に表示される
     */
    public function test_purchased_item_appears_in_mypage()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create([
            'postcode' => '123-4567',
            'address'  => '東京都渋谷区',
        ]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
            'name' => '一覧表示テスト商品',
        ]);

        $this->actingAs($buyer);

        // 購入（testing分岐が走る）
        $this->post(route('purchase.store', $item->id), [
            'payment_method' => 'konbini',
        ]);

        // ★ 購入タブを表示
        $response = $this->get(route('profile.show', ['tab' => 'buy']));

        $response->assertStatus(200);

        // 購入商品が表示されている
        $response->assertSee('一覧表示テスト商品');
    }
}
