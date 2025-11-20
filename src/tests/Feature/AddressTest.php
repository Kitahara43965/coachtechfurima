<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\PurchaseTrait;

class AddressTest extends TestCase
{
    use PurchaseTrait;

    //配送先住所変更後、購入画面に反映されているか確認
    public function testPurchaseAddressChange()
    {
        $response = $this->purchase($this->purchaseAddressChangeKind);
    }

    //配送先住所変更後購入した商品に送付先住所が紐づいて登録される
    public function testPurchaseFinishAfterAddressChange()
    {
        $response = $this->purchase($this->purchaseFinishAfterAddressChangeKind);
    }

}