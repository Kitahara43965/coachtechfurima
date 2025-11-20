<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\LoginTrait;

class LoginTest extends TestCase
{
    use LoginTrait;

    //メールアドレスが入力されていない
    public function testLoginMissingEmail(): void
    {
        $response = $this->login($this->loginMissingEmailKind);
    }

    //パスワードが入力されていない
    public function testLoginMissingPassword(): void
    {
        $response = $this->login($this->loginMissingPasswordKind);
    }

    //パスワードと確認用パスワードが一致しない
    public function testLoginWrongPassword(): void
    {
        $response = $this->login($this->loginWrongPasswordKind);
    }

    //登録が成功した場合
    public function testLoginOk(): void
    {
        $response = $this->login($this->loginOkKind);
    }
}
