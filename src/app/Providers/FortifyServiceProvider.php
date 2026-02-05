<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Fortify::ignoreRoutes();
        //
    }

    public function boot(): void
    {
        // Fortify 標準アクション
        Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(\App\Actions\Fortify\UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(\App\Actions\Fortify\UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(\App\Actions\Fortify\ResetUserPassword::class);

        // ログイン試行制限
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->session()->get('login.id')
            );
        });

        // 認証（Fortify標準）
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            return $user;
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::redirects('email-verification', '/profile/setup');
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            // ★ ログイン成功後の遷移を明示的に制御
            Auth::login($user);

            if (! $user->hasVerifiedEmail()) {
                redirect()->route('verification.notice')->send();
            }

            if (empty($user->postcode) || empty($user->address)) {
                redirect()->route('profile.setup')->send();
            }

            redirect()->route('profile.show')->send();
        });
    }
}
