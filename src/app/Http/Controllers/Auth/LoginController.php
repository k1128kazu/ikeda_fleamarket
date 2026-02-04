<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticated(Request $request, $user)
    {
        // ① メール未認証なら、必ず認証画面へ
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // ② メール認証済み ＆ プロフィール未設定なら setup
        if (
            empty($user->name) ||
            empty($user->postcode) ||
            empty($user->address)
        ) {
            return redirect()->route('profile.setup');
        }

        // ③ すべてOKならマイページ
        return redirect()->route('profile.show');
    }
}
