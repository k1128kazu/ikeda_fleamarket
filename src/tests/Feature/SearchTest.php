<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品名で検索すると一致する商品が表示される
     */
    public function test_user_can_search_items_by_keyword()
    {
        // 検索にヒットする商品
        Item::factory()->create([
            'name' => 'ナイキ スニーカー',
        ]);

        // ヒットしない商品（表示されてもOK）
        Item::factory()->create([
            'name' => 'アディダス ジャージ',
        ]);

        $response = $this->get('/?keyword=ナイキ');

        $response->assertStatus(200);

        // ★ 一致する商品が表示されることだけ保証する
        $response->assertSee('ナイキ スニーカー');
    }
}
