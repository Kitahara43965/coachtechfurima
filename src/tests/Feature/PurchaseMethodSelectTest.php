<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\PurchaseTrait;

class PurchaseMethodSelectTest extends TestCase
{
    use PurchaseTrait;

    //小計画面で変更が反映される
    public function testPurchaseMethodSelect()
    {
        $response = $this->purchase($this->purchaseMethodSelectKind);
    }
}
