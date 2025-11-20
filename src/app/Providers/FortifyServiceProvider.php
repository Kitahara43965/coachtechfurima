<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Actions\Fortify\CreateNewUser;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // ユーザー登録処理
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        // 登録ビュー
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login'); // ← ログインページのBlade
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        // ログインレートリミッター
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            if (app()->environment('local')) {
                return Limit::none(); // 開発中は制限なし
            }

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        // 登録後リダイレクト
        app()->bind(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });

        // メール認証ビュー
        app()->bind(VerifyEmailViewResponse::class, function () {
            return new class implements VerifyEmailViewResponse {
                public function toResponse($request)
                {
                    return view('auth.verify-email'); 
                }
            };
        });

    }
}
