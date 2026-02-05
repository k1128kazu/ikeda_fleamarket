<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインはマイページにアクセスできない
     */
    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get(route('profile.show'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 住所未設定ユーザーはログイン後 setup にリダイレクトされる
     */
    public function test_user_can_login_and_redirected_to_setup_if_profile_incomplete()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'postcode' => null,
            'address'  => null,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('profile.setup'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * ログアウトできる（ログイン画面へリダイレクト）
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * ログアウト後はマイページにアクセスできない
     */
    public function test_logged_out_user_cannot_access_mypage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('logout'));

        $response = $this->get(route('profile.show'));
        $response->assertRedirect(route('login'));
    }
}
