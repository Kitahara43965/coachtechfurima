<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Tests\Feature\Traits\MailTrait;
use Tests\Feature\Traits\RegisterTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;

trait LoginTrait
{
    use MailTrait,RegisterTrait;

    protected $loginUndefinedKind = 0;
    protected $loginMissingEmailKind = 1;
    protected $loginMissingPasswordKind = 2;
    protected $loginWrongPasswordKind = 3;
    protected $loginOkKind = 4;
    protected $checkLoginTime = BaseController::CHECK_LOGIN_TIME;

    public function login($loginKind)
    {
        //メール認証済み
        $response = $this->mail($this->mailUndefinedKind);
        if(Auth::check()){
            $response = $this->post(route('logout'), []);
        }

        //logoutしていることを確認
        if($loginKind !== $this->loginUndefinedKind){
            $this->assertGuest();
        }//$loginKind

        if($loginKind === $this->loginMissingEmailKind){
            $tag = 'email';
            $message = 'メールアドレスを入力してください';
            $email = $this->registerEmpty;
            $password = $this->registerPassword;
        }else if($loginKind === $this->loginMissingPasswordKind){
            $tag = 'password';
            $message = 'パスワードを入力してください';
            $email = $this->registerEmail;
            $password = $this->registerEmpty;
        }else if($loginKind === $this->loginWrongPasswordKind){
            $tag = 'email';
            $message = 'ログイン情報が登録されていません';
            $email = $this->registerEmail;
            $password = $this->registerWrongPassword;
        }else{//$loginKind
            $tag = null;
            $message = null;
            $email = $this->registerEmail;
            $password = $this->registerPassword;
        }//$loginKind

        // 入力値
        $formData = [
            'email' => $email,
            'password' => $password,
        ];

        // ログイン処理を実行
        $response = $this->post(route('login.store'), $formData);

        $user = Auth::user();

        if($loginKind !== $this->loginUndefinedKind){
            if($tag){
                //バリデーションエラーを確認
                $response->assertSessionHasErrors([
                    $tag => $message,
                ]);
            }else{
                //バリデーションエラーが出ないかを確認
                $response->assertSessionDoesntHaveErrors();
                //商品一覧画面に遷移したか確認

                $response->assertRedirect(route('verification.notice'));

                $this->assertAuthenticated();
            }//$tag
        }//$loginKind

        return($response);
    }

}