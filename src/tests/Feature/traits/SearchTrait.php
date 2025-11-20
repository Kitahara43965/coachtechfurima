<?php

namespace Tests\Feature\Traits;
use Tests\Feature\Traits\ProfileTrait;
use Tests\TestCase;

trait SearchTrait
{
    use ProfileTrait;

    protected $searchUndefinedKind = 0;
    protected $searchSelectKeywordKind = 1;
    protected $searchCheckKeywordAfterPageTransitionKind = 2;

    protected $searchKeyword = "玉ねぎ";

    public function search($searchKind)
    {
        $response = $this->profile($this->profileUndefinedKind);
        
        $keyword = $this->searchKeyword;
        $response = $this->get(route('index', ['keyword' => $keyword]));
        
        if($searchKind === $this->searchSelectKeywordKind){
            //検索して商品一覧画面に移動できたか確認
            $response->assertStatus(200);
            //キーワードが入っているか確認。ここでは初期値に「玉ねぎ3束」があるので、検索できているか確認
            $response->assertSee($keyword);
        }//$searchKind

        //マイページに移動
        $response = $this->get(route('mypage'));
        //検索した語('keyword')を取得
        $searchedKeyWorld = session('search_keyword', '');

        if($searchKind === $this->searchCheckKeywordAfterPageTransitionKind){
            //レスポンスが正常であることを確認
            $response->assertStatus(200);
            //検索した語が検索欄のセッションに保存されているかを確認
            $this->assertEquals($keyword,$searchedKeyWorld);
        }//$searchKind

        return($response);
    }
    
}