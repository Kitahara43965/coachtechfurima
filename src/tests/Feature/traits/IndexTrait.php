<?php

namespace Tests\Feature\Traits;

use Tests\Feature\Traits\PurchaseTrait;
use Tests\Feature\Traits\SellTrait;
use Tests\Feature\Traits\InitialValueTrait;
use Tests\Feature\Traits\FavoriteTrait;
use Tests\Feature\Traits\ProfileTrait;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;
use \App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait IndexTrait
{

    use InitialValueTrait,FavoriteTrait,SellTrait,PurchaseTrait;

    protected $indexUndefinedKind = 0;
    protected $indexWithoutLoginKind = 1;
    protected $indexSoldLogoCheckKind = 2;
    protected $indexWithLoginKind = 3;
    protected $indexMylistFavoriteCheckKind = 4;
    protected $indexMylistSoldLogoCheckKind = 5;
    protected $indexMylistWithoutLoginKind = 6;

    public function index($indexKind){

        $undefinedImageCheckStatus = 0;
        $allImageCheckStatus = 1;
        $onlyOwneredNotByUserImageCheckStatus = 2;
        $onlyFavoriteImageCheckStatus = 3;
        $onlyFavoriteWithoutLoginImageCheckStatus = 4;


        if($indexKind === $this->indexWithoutLoginKind){
            //商品一覧機能ー全商品を取得できる
            // ログインしてない
            $response = $this->initialValue($this->initialValueUndefinedKind);
            //商品一覧ページへ
            $route = route('index');
            $imageCheckStatus = $allImageCheckStatus;
            $soldCheckMarker = 0;
            $soldFavoriteSelectMarker = 0;
        }else if($indexKind === $this->indexSoldLogoCheckKind){
            //商品一覧機能ー購入済み商品は「sold」と表示される
            //ログイン済み（商品購入済み）
            $response = $this->purchase($this->purchaseUndefinedKind);
            $route = route('index');
            $imageCheckStatus = $undefinedImageCheckStatus;
            $soldCheckMarker = 1;
            $soldFavoriteSelectMarker = 0;
        }else if($indexKind === $this->indexWithLoginKind){
            //商品一覧機能ー自分が出品した商品は表示されない
            //ログイン済み(販売済み)
            $response = $this->sell($this->sellUndefinedKind);
            //商品一覧ページへ
            $route = route('index');
            $imageCheckStatus = $onlyOwneredNotByUserImageCheckStatus;
            $soldCheckMarker = 0;
            $soldFavoriteSelectMarker = 0;
        }else if($indexKind === $this->indexMylistFavoriteCheckKind){
            //マイページ機能ー良いねした商品だけが表示される
            //ログイン済み(お気に入り済み)
            $response = $this->favorite($this->favoriteUndefinedKind);
            //マイリストページへ
            $route = route('index',['tab' => 'mylist']);
            $imageCheckStatus = $onlyFavoriteImageCheckStatus;
            $soldCheckMarker = 0;
            $soldFavoriteSelectMarker = 1;
        }else if($indexKind === $this->indexMylistSoldLogoCheckKind){
            //マイページ機能ー購入済み商品は「sold」と表示される
            //ログイン済み(購入した商品にお気に入り済み)
            $response = $this->favorite($this->favoriteAfterPurchaseNoTestKind);
            //マイリストページへ
            $route = route('index',['tab' => 'mylist']);
            $imageCheckStatus = $onlyFavoriteImageCheckStatus;
            $soldCheckMarker = 2;
            $soldFavoriteSelectMarker = 2;
        }else if($indexKind === $this->indexMylistWithoutLoginKind){
            //マイページ機能ー自分が出品した商品は何も表示されない
            // ログインしてない
            $response = $this->initialValue($this->initialValueUndefinedKind);
            //マイリストページへ
            $route = route('index',['tab' => 'mylist']);
            $imageCheckStatus = $onlyFavoriteWithoutLoginImageCheckStatus;
            $soldCheckMarker = 0;
            $soldFavoriteSelectMarker = 3;
        }else{//$indexOkKind
            //ログイン済み
            $response = $this->profile($this->profileUndefinedKind);
            //商品一覧ページへ
            $route = route('index');
            $imageCheckStatus = $undefinedImageCheckStatus;
            $soldCheckMarker = 0;
            $soldFavoriteSelectMarker = 0;
        }//$indexOkKind

        $items = Item::all();
        $user = Auth::user();
        $response = $this->get($route);

        $html = $response->getContent();
         if($indexKind !== $this->indexUndefinedKind){
            //ページに入ったか確認
            $response->assertStatus(200);
        }//$indexKind

        foreach($items as $item){
            if($item->image){
                if($item->is_default){
                    $itemImagePath = $this->coachtechImageDirectory.'/'.$item->image;
                }else{
                    $itemImagePath = $this->itemImageDirectory.'/'.$item->image;
                }
                $itemPreviewUrl = asset('storage/' . $itemImagePath);

                if($imageCheckStatus !== $undefinedImageCheckStatus){
                    //画像が保存されているか確認
                    Storage::disk('public')->assertExists($itemImagePath);
                }//$imageCheckStatus

                //商品画像を確認
                if($imageCheckStatus == $allImageCheckStatus){
                    $response->assertSee($itemPreviewUrl, false);
                }else if($imageCheckStatus == $onlyOwneredNotByUserImageCheckStatus){
                    if(Auth::check()){
                        if($item->isOwnedBy(Auth::user())){
                            //全てのitemの中で出品したものは表示されない
                            $response->assertDontSee($itemPreviewUrl, false);
                        }else{
                            //全てのitemの中で出品していないものが表示される
                            $response->assertSee($itemPreviewUrl, false);
                        }
                    }
                }else if($imageCheckStatus == $onlyFavoriteImageCheckStatus){
                    if($item->isFavoritedBy($user)){
                        //全てのitemの中でfavoriteであるものは表示される
                        $response->assertSee($itemPreviewUrl, false);
                    }else{
                        //全てのitemの中でfavoriteでないものは表示されない
                        $response->assertDontSee($itemPreviewUrl, false);
                    }
                }else if($imageCheckStatus == $onlyFavoriteWithoutLoginImageCheckStatus){
                    $response->assertDontSee($itemPreviewUrl, false);
                }//$imageCheckStatus
                
            }//$item->image

            
            // アイテムカードの HTML を切り出す
            $pattern = '/<a[^>]*href="[^"]*item\/'.$item->id.'[^"]*"[^>]*>(.*?)<\/a>/s';
            preg_match($pattern, $html, $matches);

            $cardHtml = $matches[1] ?? '';

            $hasSold = str_contains($cardHtml, 'index-sold-text');

            $isFavoritedByUser = $item?->isFavoritedBy(Auth::user()) ?? false;
            $isPurchased = $item?->isPurchased() ?? false;


            if($soldCheckMarker !== 0){
                
                $assertTrueDenialMarker = 0;

                if($soldFavoriteSelectMarker !== 0){
                    if(!$isFavoritedByUser){
                        $assertTrueDenialMarker = 1;
                    }
                }

                if(!$isPurchased){
                    $assertTrueDenialMarker = 2;
                }

                if ($assertTrueDenialMarker === 0) {
                    $this->assertTrue($hasSold, "Item {$item->id} は購入済みなのに Sold がありません");
                } else {//$assertTrueDenialMarker
                    $this->assertFalse($hasSold, "Item {$item->id} は未購入なのに Sold が表示されています");
                }//$assertTrueDenialMarker
            }//$soldCheckMarker&0
        }

        return($response);
    }

}