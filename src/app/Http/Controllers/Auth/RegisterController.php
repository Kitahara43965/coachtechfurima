<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{

    public function show()
    {

        $isItemList = false;
        $isMultipleFunctionHeader = false;

        return view('auth.register',compact(
            'isItemList',
            'isMultipleFunctionHeader',
        )); // 登録フォーム用のBlade
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'login_time' => 0,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verification_token' => rand(100000, 999999), // 6桁の認証コード
        ]);

        // ログインしてからイベント発火
        auth()->login($user);

        // 登録イベント → メール認証通知
        event(new Registered($user));

        $user->sendEmailVerificationNotification();
        
        return redirect()->route('verification.notice'); // メール認証ページへ
    }
}