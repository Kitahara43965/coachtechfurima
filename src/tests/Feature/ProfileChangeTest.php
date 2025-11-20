<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\ProfileChangeTrait;

class ProfileChangeTest extends TestCase
{
    use ProfileChangeTrait;

     //プロファイルを変更後、各項目が保存されているか確認
    public function testProfileChangeOk(): void
    {
        $response = $this->profileChange($this->profileChangeOkKind);
    }
}
