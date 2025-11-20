<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        // 未ログインならメールリンクの {id} から自動ログイン
        if (!Auth::check()) {
            Auth::loginUsingId($request->route('id'));
        }

        // すでに認証済みの場合
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(route('index'))->with('status', 'already-verified');
        }

        // メールアドレスを認証済みにしてイベント発火
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect(route('index'))->with('status', 'email-verified');
    }

    public function emailVerify()
    {
        $checkLoginTime = BaseController::CHECK_LOGIN_TIME;
        $user = Auth::user();

        $loginTime = $user->login_time;

        if($user){
            if($user->hasVerifiedEmail()){
                if($loginTime <= $checkLoginTime){
                    $emailVerifyMarker = 1;
                }else{
                    $emailVerifyMarker = 0;
                }
            }else{
                $emailVerifyMarker = 2;
            }
        }else{
            $emailVerifyMarker = 3;
        }

        if($emailVerifyMarker === 0){
            return redirect()->route('index');
        }else{
            return view('auth.verify-email');
        }
    }


    public function verifyEmail() {
        $user = Auth::user();
        // 認証状態を更新
        $user->markEmailAsVerified();

        return redirect()
            ->route('mypage.profile')
            ->with('status', 'メール認証が完了しました！');
    }

    public function emailVerifyIdHash(EmailVerificationRequest $request){
        $request->fulfill(); // メール認証完了
        return redirect()->route('mypage.profile'); // 認証後のリダイレクト先
    }
}
