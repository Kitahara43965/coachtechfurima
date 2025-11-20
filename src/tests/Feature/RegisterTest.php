<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\RegisterTrait;

class RegisterTest extends TestCase
{
    use RegisterTrait;

    //名前が入力されていない
    public function testRegisterMissingName(): void
    {
        $response = $this->register($this->registerMissingNameKind);
    }

    //メールアドレスが入力されていない
    public function testRegisterMissingEmail(): void
    {
        $response = $this->register($this->registerMissingEmailKind);
    }

    //パスワードが入力されていない
    public function testRegisterMissingPassword(): void
    {
        $response = $this->register($this->registerMissingPasswordKind);
    }

    //パスワードが8文字以下
    public function testRegisterShortPassword(): void
    {
        $response = $this->register($this->registerShortPasswordKind);
    }

    //パスワードと確認用パスワードが一致しない
    public function testRegisterWrongPassword(): void
    {
        $response = $this->register($this->registerWrongPasswordKind);
    }

    //登録が成功した場合
    public function testRegisterOk(): void
    {
        $response = $this->register($this->registerOkKind);
    }
}
