<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\IndexTrait;

class IndexTest extends TestCase
{
    use IndexTrait;

    //ログインしないで商品一覧画面に入った時は全商品表示されることを確認
    public function testIndexWithoutLogin(): void
    {
        $response = $this->index($this->indexWithoutLoginKind);
    }

    //購入した商品にSoldが表示されるか確認
    public function testIndexSoldLogoCheck(): void
    {
        $response = $this->index($this->indexSoldLogoCheckKind);
    }

    //ログインして商品一覧画面に入った時は出品した商品が出てきていないことを確認
    public function testIndexWithLogin(): void
    {
        $response = $this->index($this->indexWithLoginKind);
    }

}
