<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{

    public function show(Request $request)
    {
        $isItemList = false;
        $isMultipleFunctionHeader = false;

        return view('auth.login', compact(
            'isItemList',
            'isMultipleFunctionHeader',
        ));
    }

    public function store(LoginRequest $request)
    {
        // LoginRequest 内の authenticate() で認証とエラーメッセージ処理を行う
        $request->authenticate();

        $user = Auth::user();

        if ($user) {
             // ログインしたので、login_timeを更新する
            $user->login_time += 1;
            // 保存して変更を反映
            $user->save();

            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('index')); // ログイン後のリダイレクト先

    }

    public function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
