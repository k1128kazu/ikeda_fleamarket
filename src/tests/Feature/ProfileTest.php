<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインの場合、マイページにアクセスするとログイン画面にリダイレクトされる
     */
    public function test_guest_is_redirected_to_login_when_accessing_mypage()
    {
        $response = $this->get('/mypage');

        $response->assertRedirect('/login');
    }

    /**
     * メール未認証ユーザーはマイページではなく認証誘導画面に遷移する
     */
    public function test_unverified_user_is_redirected_to_email_verification()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertRedirect('/email/verify');
    }

    /**
     * メール認証済みだがプロフィール未設定の場合、初回プロフィール設定画面に遷移する
     */
    public function test_verified_user_without_profile_is_redirected_to_profile_setup()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'image' => null,
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertRedirect('/mypage/setup');
    }

    /**
     * 初回プロフィール設定画面が表示される
     */
    public function test_profile_setup_page_can_be_displayed()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'image' => null,
        ]);

        $response = $this->actingAs($user)->get('/mypage/setup');

        $response->assertStatus(200);
        $response->assertViewIs('profile.setup');
    }

    /**
     * 初回プロフィール設定を保存でき、マイページへ遷移する
     */
    public function test_initial_profile_can_be_saved()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'image' => null,
        ]);

        $response = $this->actingAs($user)->post('/mypage/setup', [
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address01' => '東京都渋谷区',
            'address02' => 'テストビル101',
            'profile_image' => UploadedFile::fake()->image('profile.png'),
        ]);

        $response->assertRedirect('/mypage');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テストユーザー',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
        ]);    }

    /**
     * プロフィール設定済みユーザーはマイページを表示できる
     */
    public function test_user_with_profile_can_view_mypage()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'image' => 'profile/test.png',
            'postcode' => '123-4567',
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertViewIs('profile.show');
    }

    /**
     * プロフィール編集画面が表示される
     */
    public function test_profile_edit_page_can_be_displayed()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'image' => 'profile/test.png',
            'postcode' => '123-4567',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }

    /**
     * プロフィールを更新できる
     */
    public function test_profile_can_be_updated()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'image' => 'profile/old.png',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            '_method' => 'PUT',
            'name' => '更新後ユーザー',
            'postcode' => '987-6543',
            'address' => '大阪府大阪市',
            'building' => '更新ビル202',
            'image' => UploadedFile::fake()->image('new.png'),
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新後ユーザー',
            'postcode' => '987-6543',
            'address' => '大阪府大阪市',
            'building' => '更新ビル202',
        ]);
    }
}
