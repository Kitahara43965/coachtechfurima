<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\LogoutTrait;

class LogoutTest extends TestCase
{
    use LogoutTrait;

    //profile入力済みであり認証済みでlogoutできることを確認
    public function testLogoutOk()
    {
        $response = $this->logout($this->logoutOkKind);
    }

}
