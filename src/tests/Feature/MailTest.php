<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\MailTrait;

class MailTest extends TestCase
{
    use MailTrait;

    //登録したメールアドレス宛に認証アドレスが送られている
    public function testMailRegistration(): void
    {
        $response = $this->mail($this->mailRegistrationKind);
    }

    //メール認証導線画面を表示する
    public function testMailVerification(): void
    {
        $response = $this->mail($this->mailVerificationKind);
    }

    //プロフィール設定画面に遷移する
    public function testMailActionToProfile(): void
    {
        $response = $this->mail($this->mailActionToProfileKind);
    }

}
