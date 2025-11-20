<?php

namespace Tests\Feature\Traits;
use Tests\Feature\Traits\ProfileTrait;
use Tests\Feature\Traits\PurchaseTrait;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \App\Models\Item;

trait FavoriteTrait
{
    use ProfileTrait,PurchaseTrait;

    protected $favoriteUndefinedKind = 0;
    protected $favoriteTurnOnKind = 1;
    protected $favoriteTurnOnIconKind = 2;
    protected $favoriteTurnOffKind = 3;
    protected $favoriteTurnOffIconKind = 4;
    protected $favoriteAfterPurchaseNoTestKind = 5;

    public function favorite($favoriteKind)
    {
        // プロフィール登録完了（前提）

        if($favoriteKind === $this->favoriteTurnOnKind){
            $response = $this->profile($this->profileUndefinedKind);
            $randomItem = Item::all()->random();
            $isAssertCheck = true;
            $isDoubleClick = true;
        }else if($favoriteKind === $this->favoriteTurnOnIconKind){
            $response = $this->profile($this->profileUndefinedKind);
            $randomItem = Item::all()->random();
            $isAssertCheck = true;
            $isDoubleClick = true;
        }else if($favoriteKind === $this->favoriteTurnOffKind){
            $response = $this->profile($this->profileUndefinedKind);
            $randomItem = Item::all()->random();
            $isAssertCheck = true;
            $isDoubleClick = true;
        }else if($favoriteKind === $this->favoriteTurnOffIconKind){
            $response = $this->profile($this->profileUndefinedKind);
            $randomItem = Item::all()->random();
            $isAssertCheck = true;
            $isDoubleClick = true;
        }else if($favoriteKind === $this->favoriteAfterPurchaseNoTestKind){
            //購入した商品にいいねをする準備
            $response = $this->purchase($this->purchaseUndefinedKind);
            //購入した商品からお気に入りをつける(IndexTest.phpで使用される)
            $randomItem = Auth::user()
                ?->purchasedItems()
                ->wherePivot('purchase_quantity', '>=', 1)
                ->get()->random();
            $isAssertCheck = false;
            $isDoubleClick = false;
        }else{//$favoriteKind
            $response = $this->profile($this->profileUndefinedKind);
            $randomItem = Item::all()->random();
            $isAssertCheck = false;
            $isDoubleClick = false;
        }//$favoriteKind
        
        $user = Auth::user();

        $randomItemId = $randomItem->id;

        if ($isAssertCheck) {
            // randomItem が items テーブルに含まれるか確認
            $this->assertTrue(Item::all()->pluck('id')->contains($randomItemId));
        }

        // item のページにアクセス（存在確認）
        $response = $this->get(route('item.item_id', ['item_id' => $randomItemId]));
        if ($isAssertCheck) {
            $response->assertStatus(200);
        }

        // ユーザーのfavoriteItemsを取得して初期状態を判定
        $user->load('favoriteItems');
        $firstFavoriteItems = $user->favoriteItems;
        $isInitiallyFavorited = $firstFavoriteItems->contains($randomItem);

        // SVG をファイルから読み込む
        $svgFilledHeart = file_get_contents(storage_path('app/public/svg/filled-heart.svg'));
        $svgHeart = file_get_contents(storage_path('app/public/svg/heart.svg'));

        // ---------- 1 回目のクリック ----------
        $response = $this->followingRedirects()
                         ->post(route('item.item_id.favorite', ['item_id' => $randomItemId]), []);


        // DB 関係を再ロード
        $user->load('favoriteItems');
        $secondFavoriteItems = $user->favoriteItems;

        // 1回目のクリック後検証
        if($isInitiallyFavorited){
            if($favoriteKind === $this->favoriteTurnOffKind){
                //お気に入りに$randomItemが含まれていないことを確認
                $this->assertFalse($secondFavoriteItems->contains($randomItem));
            }
            if($favoriteKind === $this->favoriteTurnOffIconKind){
                // アイコンが塗りつぶされていないことを確認
                $response->assertSee($svgHeart, false);
            }
        }else{
            if($favoriteKind === $this->favoriteTurnOnKind){
                //お気に入りに$randomItemが含まれていることを確認
                $this->assertTrue($secondFavoriteItems->contains($randomItem));
            }
            if($favoriteKind === $this->favoriteTurnOnIconKind){
                // アイコンが塗りつぶされていることを確認
                $response->assertSee($svgFilledHeart, false);
            }
        }

        if($isDoubleClick){
            // ---------- 2 回目のクリック (toggle back) ----------
            $response = $this->followingRedirects()
                            ->post(route('item.item_id.favorite', ['item_id' => $randomItemId]), []);
            $user->load('favoriteItems');
            $thirdFavoriteItems = $user->favoriteItems;

            // 2回目のクリック後検証（同様に）
            if($isInitiallyFavorited){
                if($favoriteKind === $this->favoriteTurnOnKind){
                    //お気に入りに$randomItemが含まれていることを確認
                    $this->assertTrue($thirdFavoriteItems->contains($randomItem));
                }
                if($favoriteKind === $this->favoriteTurnOnIconKind){
                    // アイコンが塗りつぶされていることを確認
                    $response->assertSee($svgFilledHeart, false);
                }
            }else{
                if($favoriteKind === $this->favoriteTurnOffKind){
                    //お気に入りに$randomItemが含まれていないことを確認
                    $this->assertFalse($thirdFavoriteItems->contains($randomItem));
                }
                if($favoriteKind === $this->favoriteTurnOffIconKind){
                    // アイコンが塗りつぶされていないことを確認
                    $response->assertSee($svgHeart, false);
                }
            }
        }//$isDoubleClick

        return $response;
    }
}
