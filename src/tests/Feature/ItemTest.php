<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Category;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出品画面が表示できる
     */
    public function test_item_create_page_can_be_displayed()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567', // プロフィール設定済み
        ]);

        $response = $this->actingAs($user)->get('/sell');

        $response->assertStatus(200);
        $response->assertViewIs('items.create');
    }

    public function test_item_can_be_created()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
        ]);

        // ★ カテゴリ作成（必須）
        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'テスト商品',
            'brand' => 'ノーブランド',
            'description' => 'テスト商品の説明です',
            'price' => 3000,
            'condition' => '良好',
            'image' => UploadedFile::fake()->image('item.png'),
            'categories' => [$category->id], // ★ 必須
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'ノーブランド',
            'price' => 3000,
            'is_sold' => false,
            'user_id' => $user->id,
        ]);
    }
    public function test_item_list_can_be_displayed()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
        ]);

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '一覧表示テスト商品',
            'is_sold' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('items.index');
        $response->assertSee('一覧表示テスト商品');
    }
    public function test_item_detail_can_be_displayed()
    {
        $item = \App\Models\Item::factory()->create([
            'name' => '詳細表示テスト商品',
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertViewIs('items.show');
        $response->assertSee('詳細表示テスト商品');
    }
}
