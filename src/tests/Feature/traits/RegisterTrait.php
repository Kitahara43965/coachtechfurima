<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Traits\InitialValueTrait;

trait RegisterTrait
{
    use InitialValueTrait;

    protected $registerUndefinedKind = 0;
    protected $registerMissingNameKind = 1;
    protected $registerMissingEmailKind = 2;
    protected $registerMissingPasswordKind = 3;
    protected $registerShortPasswordKind = 4;
    protected $registerWrongPasswordKind = 5;
    protected $registerOkKind = 6;

    protected $registerEmpty = '';
    protected $registerName = '山田 太郎';
    protected $registerEmail = 'test@example.com';
    protected $registerPassword = 'password123';
    protected $registerShortPassword = 'passwor';
    protected $registerWrongPassword = 'password987';


    function register($registerKind){

        $response = $this->initialValue($this->initialValueUndefinedKind);
        
        $response = $this->get(route('register'));
        
         // 登録ページへアクセスできるか確認
        if($registerKind !== $this->registerUndefinedKind){
            $response->assertStatus(200);
        }

        if($registerKind === $this->registerMissingNameKind){
            $tag = 'name';
            $message = 'お名前を入力してください';
            $name = $this->registerEmpty;
            $email = $this->registerEmail;
            $password = $this->registerPassword;
            $passwordConfirmation = $this->registerPassword;
        }else if($registerKind === $this->registerMissingEmailKind){
            $tag = 'email';
            $message = 'メールアドレスを入力してください';
            $name = $this->registerName;
            $email = $this->registerEmpty;
            $password = $this->registerPassword;
            $passwordConfirmation = $this->registerPassword;
        }else if($registerKind === $this->registerMissingPasswordKind){
            $tag = 'password';
            $message = 'パスワードを入力してください';
            $name = $this->registerName;
            $email = $this->registerEmail;
            $password = $this->registerEmpty;
            $passwordConfirmation = $this->registerPassword;
        }else if($registerKind === $this->registerShortPasswordKind){
            $tag = 'password';
            $message = 'パスワードは8文字以上で入力してください';
            $name = $this->registerName;
            $email = $this->registerEmail;
            $password = $this->registerShortPassword;
            $passwordConfirmation = $this->registerShortPassword;
        }else if($registerKind === $this->registerWrongPasswordKind){
            $tag = 'password';
            $message = 'パスワードと一致しません';
            $name = $this->registerName;
            $email = $this->registerEmail;
            $password = $this->registerPassword;
            $passwordConfirmation = $this->registerWrongPassword;
        }else{//$registerKind
            $tag = null;
            $message = null;
            $name = $this->registerName;
            $email = $this->registerEmail;
            $password = $this->registerPassword;
            $passwordConfirmation = $this->registerPassword;
        }//$registerKind

        // 入力値（name を空に）
        $formData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ];

        // 登録処理を実行
        $response = $this->post(route('register.store'), $formData);

        if($registerKind !== $this->registerUndefinedKind){
            if($tag){
                //バリデーションエラーを確認
                $response->assertSessionHasErrors([
                    $tag => $message,
                ]);
            }else{
                //バリデーションエラーが出ないかを確認
                $response->assertSessionDoesntHaveErrors();
                //登録できているかを確認
                $this->assertDatabaseHas('users', [
                    'email' => $this->registerEmail,
                ]);
                $this->assertAuthenticated();
            }//$tag
        }//$registerKind

        return($response);
    }
}