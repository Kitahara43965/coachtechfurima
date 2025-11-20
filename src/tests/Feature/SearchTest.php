<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\SearchTrait;

class SearchTest extends TestCase
{
    use SearchTrait;

    //キーワードが部分検索できているか確認
    public function testSearchSelectKeyword()
    {
        $response = $this->search($this->searchSelectKeywordKind);
    }

    //マイページ（プロフィール）画面に移動してもキーワードは消えていないか確認
    public function testSearchCheckKeywordAfterPageTransition()
    {
        $response = $this->search($this->searchCheckKeywordAfterPageTransitionKind);
    }
}
