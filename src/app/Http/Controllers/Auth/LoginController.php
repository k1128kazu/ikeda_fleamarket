<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        // ★ ここで明示的にバリデーション（日本語）
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => '正しいメールアドレス形式で入力してください',
                'password.required' => 'パスワードを入力してください',
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // 既存の遷移ロジック
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (empty($user->name) || empty($user->postcode) || empty($user->address)) {
            return redirect()->route('profile.setup');
        }

        return redirect()->route('profile.show');
    }
}
