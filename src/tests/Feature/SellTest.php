<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\sellTrait;

class SellTest extends TestCase
{
    use SellTrait;

    //出品できたか確認
    public function testSellOk()
    {
        $response=$this->sell($this->sellOkKind);
    }

}
