<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常に会員登録できる
     */
    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // ★ 現在の仕様に合わせる
        $response->assertRedirect('/mypage/setup');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name'  => 'テスト太郎',
        ]);
    }

    /**
     * 名前未入力では登録できない
     */
    public function test_register_requires_name()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * メール未入力では登録できない
     */
    public function test_register_requires_email()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが8文字未満では登録できない
     */
    public function test_register_requires_password_min_length()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * パスワード確認が一致しないと登録できない
     */
    public function test_register_requires_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);

        $response->assertSessionHasErrors('password');
    }
    /**
     * 会員登録時にメール認証通知が送信される
     */
    public function test_verification_email_is_sent_on_register()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'verify@example.com')->first();

        // 認証メール（通知）が送信されていることを確認
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }
}
