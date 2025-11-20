<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Traits\InitialValueTrait;
use Tests\Feature\Traits\ProfileTrait;
use Tests\Feature\Traits\SellTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use \App\Models\Item;

trait ProfileCheckTrait
{
    use InitialValueTrait,ProfileTrait,SellTrait;

    protected $profileCheckUndefinedKind = 0;
    protected $profileCheckOkKind = 1;

    public function profileCheck($profileCheckKind){

        // ユーザー・プロフィール登、購入・販売済み
        $response = $this->sell($this->sellAfterPurchaseNoTestKind);
        //プロフィール画面に入る
        $response = $this->get(route('mypage.profile'));
        //改めてuser情報を取得します。
        $user = Auth::user();
        $userImagePath = $this->profileUserImageDirectory.'/'.$user->image;

        if($profileCheckKind !== $this->profileCheckUndefinedKind){
            //プロフィール画面が有効か確認
            $response->assertStatus(200);

            //ユーザー名を確認
            $response->assertSee($user->username);

            //プロフィール画像がフォルダーに存在するか確認
            Storage::disk('public')->assertExists($userImagePath);

            //画像を確認
            $userPreviewUrl = asset('storage/' . $userImagePath);
            $response->assertSee($userPreviewUrl, false);

        }

        //購入したitem全て
        $purchasedItems = $user->purchasedItems()->get();
        //出品したitem全て
        $ownedItems = $user->ownedItems()->get();


        for($loopTime=1;$loopTime<=2;$loopTime++){

            $items = Item::all();

            if($loopTime === 1){
                //購入した商品一覧
                $response = $this->get(route('mypage',['page'=>'buy']));
            }else if($loopTime === 2){
                //出品した商品一覧
                $response = $this->get(route('mypage',['page'=>'sell']));
            }//$loopTime
            
            if($profileCheckKind !== $this->profileCheckUndefinedKind){
                $response->assertStatus(200);
            }

            foreach($items as $item){
                if($item->image){
                    if($item->is_default){
                        $itemImagePath = $this->coachtechImageDirectory.'/'.$item->image;
                    }else{
                        $itemImagePath = $this->itemImageDirectory.'/'.$item->image;
                    }
                    
                    
                    $itemPreviewUrl = asset('storage/' . $itemImagePath);
                    if($profileCheckKind !== $this->profileCheckUndefinedKind){
                        //画像が保存されているか確認
                        Storage::disk('public')->assertExists($itemImagePath);
                    }

                    $isOwnedByUser = $item->isOwnedBy($user);
                    $isPurchasedByUser =$item->isPurchasedBy($user);

                    $isImageToBeVisible = false;
                    if($loopTime === 1){
                        //購入した商品一覧
                        if($isPurchasedByUser){
                            $isImageToBeVisible = true;
                        }//$isPurchasedByUser
                    }else if($loopTime === 2){
                        //出品した商品一覧
                        if($isOwnedByUser){
                            $isImageToBeVisible = true;
                        }
                    }//$loopTime

                    if($profileCheckKind !== $this->profileCheckUndefinedKind){
                        if($isImageToBeVisible){
                            $response->assertSee($itemPreviewUrl, false);
                        }else{//$isImageToBeVisible
                            $response->assertDontSee($itemPreviewUrl, false);
                        }//$isImageToBeVisible
                    }//$profileCheckKind

                }//$item->image
            }//foreach
        }//$loopTime

        return($response);
    }

}