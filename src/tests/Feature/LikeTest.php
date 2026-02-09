<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはいいねできない
     */
    public function test_guest_cannot_like_item()
    {
        $item = Item::factory()->create();

        $response = $this->post("/like/{$item->id}");

        // 認証ミドルウェアによりログインへ
        $response->assertRedirect('/login');
    }

    /**
     * ログインユーザーはいいねできる
     */
    public function test_user_can_like_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/like/{$item->id}");

        $response->assertStatus(302);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * ログインユーザーはいいね解除できる
     */
    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // いいね追加
        $this->post("/like/{$item->id}");

        // いいね解除
        $this->delete("/like/{$item->id}");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
