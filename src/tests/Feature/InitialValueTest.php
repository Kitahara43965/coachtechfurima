<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\InitialValueTrait;

class InitialValueTest extends TestCase
{
    use InitialValueTrait;

    //初期化・シーディング・初期の画像保存の確認
    public function testInitialValueOk():void
    {
        $response = $this->initialValue($this->initialValueOkKind);
    }
}
