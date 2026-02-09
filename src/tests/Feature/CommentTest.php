<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはコメントできない
     */
    public function test_guest_cannot_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/comment/{$item->id}", [
            'content' => 'テストコメント',
        ]);

        // 認証ミドルウェアによりログイン画面へ
        $response->assertRedirect('/login');
    }

    /**
     * ログインユーザーはコメント投稿できる
     */
    public function test_user_can_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/comment/{$item->id}", [
            'content' => 'テストコメント',
        ]);

        // 投稿後はリダイレクトされる想定
        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    /**
     * コメント未入力では投稿できない
     */
    public function test_comment_requires_content()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/comment/{$item->id}", [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }
}
