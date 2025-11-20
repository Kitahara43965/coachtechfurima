<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\ProfileCheckTrait;

class ProfileCheckTest extends TestCase
{
    use ProfileCheckTrait;

    //必要な情報が取得できる
    public function testProfileCheckOk(): void
    {
        $response = $this->profileCheck($this->profileCheckOkKind);
    }

}
