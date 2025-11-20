<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\IndexTrait;

class MylistTest extends TestCase
{
    use IndexTrait;

    //マイリストでお気に入りの商品のみが表示されることを確認
    public function testIndexMylistFavoriteCheck(): void
    {
        $response = $this->index($this->indexMylistFavoriteCheckKind);
    }

    //マイリストで購入された商品にSoldが表示されることを確認
    public function testIndexMylistSoldLogoCheck(): void
    {
        $response = $this->index($this->indexMylistSoldLogoCheckKind);
    }

    
    public function testIndexMylistWithoutLogin(): void
    {
        $response = $this->index($this->indexMylistWithoutLoginKind);
    }


}
