<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 未ログインならログイン画面へ（authミドルウェアが先に弾くはず）
        if (! $user) {
            return redirect()->route('login');
        }

        // メール未認証なら認証ページへ
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // プロフィール未登録なら別ページへ
        if ($user->is_filled_with_profile == false) {
            // 例: プロフィール登録ページに誘導
            return redirect()->route('mypage.profile')
                ->with('error', 'プロフィールを登録してください。');
        }

        // すべてOKなら通す
        return $next($request);
    }
}
