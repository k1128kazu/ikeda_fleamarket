<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        Auth::guard('web')->attempt(
            $request->only('email', 'password')
        );

        $request->session()->regenerate();

        // 🔽 初回ログイン判定
        if (auth()->user()->isProfileIncomplete()) {
            return redirect()->route('profile.setup');
        }

        return redirect()->route('items.index');
    }
}
