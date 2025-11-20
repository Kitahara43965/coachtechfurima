<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\FavoriteTrait;

class FavoriteTest extends TestCase
{
    use FavoriteTrait;

    //お気に入りをつけられるかを確認
    public function testFavoriteTurnOn()
    {
        $response = $this->favorite($this->favoriteTurnOnKind);
    }

    //お気に入りをつけたときにアイコン(ハート)が黒塗りになるかを確認
    public function testFavoriteTurnOnIcon()
    {
        $response = $this->favorite($this->favoriteTurnOnIconKind);
    }

    //お気に入りを解除できたかを確認
    public function testFavoriteTurnOff()
    {
        $response = $this->favorite($this->favoriteTurnOffKind);
    }

    //お気に入りを解除したときにアイコン(ハート)が黒塗りでなくなるかを確認
    public function testFavoriteTurnOffIcon()
    {
        $response = $this->favorite($this->favoriteTurnOffIconKind);
    }

}
