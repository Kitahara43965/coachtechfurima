<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\ProfileTrait;

class ProfileTest extends TestCase
{
    use ProfileTrait;

    //プロファイルの登録完了
    public function testProfileOk(): void
    {
        $response = $this->profile($this->profileOkKind);
    }
}
