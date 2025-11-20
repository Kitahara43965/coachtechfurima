<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\PurchaseTrait;

class PurchaseTest extends TestCase
{
    use PurchaseTrait;

    //購入が完了したか確認
    public function testPurchaseFinish()
    {
        $response = $this->purchase($this->purchaseFinishKind);
    }

    //商品一覧にてsoldが表示されるか確認
    public function testPurchaseSold()
    {
        $response = $this->purchase($this->purchaseSoldKind);
    }

    //mypageの購入した商品一覧画面で購入された商品が追加されていることを確認
    public function testPurchaseViewCheck()
    {
        $response = $this->purchase($this->purchaseViewCheckKind);
    }
}
