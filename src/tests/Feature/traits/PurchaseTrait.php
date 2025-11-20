<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Traits\ProfileTrait;
use \App\Models\Item;
use \App\Models\PurchaseMethod;


trait PurchaseTrait
{
    use ProfileTrait;

    protected $purchaseUndefinedKind = 0;
    protected $purchaseFinishKind = 1;
    protected $purchaseSoldKind = 2;
    protected $purchaseViewCheckKind = 3;
    protected $purchaseAddressChangeKind = 4;
    protected $purchaseFinishAfterAddressChangeKind = 5;
    protected $purchaseMethodSelectKind = 6;


    protected $purchasePostcode = "234-5678";
    protected $purchaseAddress = "愛知県名古屋市緑区";
    protected $purchaseBuilding = "緑ビル";

    public function purchase($purchaseKind){

        //profile入力済みでloginする
        $response = $this->profile($this->profileUndefinedKind);

        if($purchaseKind === $this->purchaseUndefinedKind){
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }else if($purchaseKind === $this->purchaseFinishKind){
            //商品購入機能 ー 「購入する」ボタンを押下すると購入が完了する
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }else if($purchaseKind === $this->purchaseSoldKind){
            //商品購入機能 ー 購入した商品は商品一覧画面にて「sold」と表示される
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }else if($purchaseKind === $this->purchaseViewCheckKind){
            //商品購入機能 ー 「プロフィール/購入した商品一覧」に追加されている
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }else if($purchaseKind === $this->purchaseAddressChangeKind){
            //配送先変更機能 ー 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
            $addressChangeMarker = 1;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 1;
        }else if($purchaseKind === $this->purchaseFinishAfterAddressChangeKind){
            //配送先変更機能 ー 購入した商品に送付先住所が紐づいて登録される
            $addressChangeMarker = 2;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }else if($purchaseKind === $this->purchaseMethodSelectKind){
            //支払い方法選択購入機能 ー 小計画面で変更が反映される
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 1;
            $purchaseDenialMarker = 1;
        }else{//$purchaseKind
            $addressChangeMarker = 0;
            $purchaseMethodChangeMarker = 0;
            $purchaseDenialMarker = 0;
        }//$purchaseKind

        $purchaseMethods = PurchaseMethod::all();
        $randomPurchaseMethod = $purchaseMethods->random();
        $randomPurchaseMethodId = $randomPurchaseMethod->id;

        //ユーザー情報取得
        $previousUser = Auth::user();
        $previousUserId = $previousUser->id;

        //販売中のアイテムを取得
        $availableItems = Item::whereDoesntHave('purchasedByUsers') // 購入されていない
            ->whereDoesntHave('usersByOwnership', function ($query) use ($previousUserId) {
                $query->where('users.id', $previousUserId); // 自分が出品したアイテムを除外
            })
            ->get();

        if ($availableItems->isNotEmpty()) {
            $randomItem = $availableItems->random();
        } else {
            $randomItem = null; // アイテムがない場合
        }

        $randomItemId = $randomItem?->id;


        $previousSelectedPurchaseTypedPivot = $previousUser->purchasedItems()
                        ?->wherePivot('item_id', $randomItemId)
                        ?->wherePivot('type', 'purchase')
                        ?->latest('created_at')
                        ?->first();

        if($previousSelectedPurchaseTypedPivot){
            $previousCandidatePurchaseMethodId = $previousSelectedPurchaseTypedPivot?->pivot?->purchase_method_id;
            $previousIsFilledWithDeliveryAddress = $previousSelectedPurchaseTypedPivot?->pivot?->is_filled_with_delivery_address;
            $previousAddress = $previousSelectedPurchaseTypedPivot?->pivot?->delivery_address;
            $previousPostcode = $previousSelectedPurchaseTypedPivot?->pivot?->delivery_postcode;
            $previousBuilding = $previousSelectedPurchaseTypedPivot?->pivot?->delivery_building;
        }else{//$previousSelectedPurchaseTypedPivot
            $previousCandidatePurchaseMethodId = null;
            $previousIsFilledWithDeliveryAddress = $previousUser->is_filled_with_profile;
            $previousAddress = $previousUser->address;
            $previousPostcode = $previousUser->postcode;
            $previousBuilding = $previousUser->building;
        }//$previousSelectedPurchaseTypedPivot


        //配送先住所を変更
        if($addressChangeMarker !== 0){
            //配送先変更画面に移動
            $response = $this->get(route('purchase.address.item_id',['item_id' => $randomItemId]));
            if($purchaseKind !== $this->purchaseUndefinedKind){
                $response->assertStatus(200);
            }

            //アドレスを変更
            $formData = [
                'delivery_address' => $this->purchaseAddress,
                'delivery_postcode' => $this->purchasePostcode,
                'delivery_building' => $this->purchaseBuilding,
            ];

            //住所を登録
            $response = $this->post(route('purchase.address.update.item_id',['item_id'=>$randomItemId]), $formData);

            if($purchaseKind !== $this->purchaseUndefinedKind){
                //配送先住所を変更したときにバリデーションエラーが出ていないことを確認
                $response->assertSessionDoesntHaveErrors();
                
                $response->assertRedirect(route('purchase.item_id', ['item_id' => $randomItemId]));

                $response = $this->get(route('purchase.item_id',['item_id' => $randomItemId]));

                //購入画面に戻ってきたことを確認
                $response->assertStatus(200);

                //データベースに変更先住所が保存できているか確認
                $this->assertDatabaseHas('user_item', [
                    'user_id' => $previousUser->id,
                    'item_id' => $randomItemId,
                    'type' => 'purchase',
                    'delivery_address' => $this->purchaseAddress,
                    'delivery_postcode' => $this->purchasePostcode,
                    'delivery_building' => $this->purchaseBuilding,
                ]);
            }//
        }//$addressChangeMarker&0

        if($purchaseMethodChangeMarker !== 0){
            //購入ページに入る
            $response = $this->get(route('purchase.item_id',['item_id' => $randomItemId]));
            if($purchaseKind !== $this->purchaseUndefinedKind){
                $response->assertStatus(200);
            }

            $formData = [
                'purchase_method_id' => $randomPurchaseMethodId,
                'is_filled_with_delivery_address' => $previousIsFilledWithDeliveryAddress,
                'delivery_address' => $previousAddress,
                'delivery_postcode' => $previousPostcode,
                'delivery_building' => $previousBuilding,
            ];

            //購入方法を変更(js/custom-select.jsに実装されている)
            $response = $this->postJson(route('purchase.update-method',['item_id' => $randomItemId]), $formData);

            if($purchaseKind !== $this->purchaseUndefinedKind){
                //購入方法選択後にページを開けているか確認
                $response->assertStatus(200);

                //配送先住所を変更したときにバリデーションエラーが出ていないことを確認
                $response->assertSessionDoesntHaveErrors();

                //データベースに変更先住所が保存できているか確認
                $this->assertDatabaseHas('user_item', [
                    'user_id' => $previousUser->id,
                    'item_id' => $randomItemId,
                    'type' => 'purchase',
                    'purchase_method_id' => $randomPurchaseMethodId,
                ]);
            }

        }//$purchaseMethodChangeMarker&0

        $nextUser = Auth::user();

        //$this->post(route('purchase.address.update.item_id',['item_id'=>$randomItemId])や
        //$this->postJson(route('purchase.update-method',['item_id' => $randomItemId])
        //で$nextSelectedPurchaseTypedPivotが生成されます。
        //下記のroute('purchase.store.item_id', ['item_id' => $randomItem->id])でも同様です。

        $nextSelectedPurchaseTypedPivot = $nextUser->purchasedItems()
                        ?->wherePivot('item_id', $randomItemId)
                        ?->wherePivot('type', 'purchase')
                        ?->latest('created_at')
                        ?->first();

        if($nextSelectedPurchaseTypedPivot){
            $nextCandidatePurchaseMethodId = $nextSelectedPurchaseTypedPivot?->pivot?->purchase_method_id;
            $nextIsFilledWithDeliveryAddress = $nextSelectedPurchaseTypedPivot?->pivot?->is_filled_with_delivery_address;
            $nextAddress = $nextSelectedPurchaseTypedPivot?->pivot?->delivery_address;
            $nextPostcode = $nextSelectedPurchaseTypedPivot?->pivot?->delivery_postcode;
            $nextBuilding = $nextSelectedPurchaseTypedPivot?->pivot?->delivery_building;
        }else{//$nextSelectedPurchaseTypedPivot
            $nextCandidatePurchaseMethodId = null;
            $nextIsFilledWithDeliveryAddress = $nextUser->is_filled_with_profile;
            $nextAddress = $nextUser->address;
            $nextPostcode = $nextUser->postcode;
            $nextBuilding = $nextUser->building;
        }//$nextSelectedPurchaseTypedPivot
        
        //purchase_method_idがない場合、randomPurchaseMethodIdを代入
        if($nextCandidatePurchaseMethodId){
            $nextPurchaseMethodId = $nextCandidatePurchaseMethodId;
        }else{//$nextCandidatePurchaseMethodId
            $nextPurchaseMethodId = $randomPurchaseMethodId;
        }//$nextCandidatePurchaseMethodId

        $nextPurchaseMethod = PurchaseMethod::findOrFail($nextPurchaseMethodId);
        $nextPurchaseMethodName = $nextPurchaseMethod->name;

        $response = $this->get(route('purchase.item_id',['item_id' => $randomItemId]));
        if($purchaseKind !== $this->purchaseUndefinedKind){
            $response->assertStatus(200);
        }

        if($purchaseDenialMarker === 0){

            //購入方法、アドレス入力済み
            $dataForm = [
                'purchase_method_id' => $nextPurchaseMethodId,
                'is_filled_with_delivery_address' => true,
                'delivery_address' => $nextAddress,
                'delivery_postcode' => $nextPostcode,
                'delivery_building' => $nextBuilding,
            ];

            //商品を購入する
            $response = $this->followingRedirects()->post(
                route('purchase.store.item_id', ['item_id' => $randomItem->id]),
                $dataForm
            );

            if($purchaseKind !== $this->purchaseUndefinedKind){
                //購入したことによってバリデーションエラーがないか確認
                $response->assertSessionDoesntHaveErrors();

                if($purchaseKind === $this->purchaseFinishKind){
                    //商品を購入できたか確認
                    $this->assertTrue($nextUser->purchasedItems->contains($randomItem));
                }//$purchaseKind
            }

            $response = $this->get(route('index'));
            $newItems = Item::all();

            if($purchaseKind === $this->purchaseSoldKind){
                //商品一覧画面に来たか確認
                $response->assertStatus(200);

                $html = $response->getContent();

                foreach ($newItems as $newItem) {

                    // アイテムカードの HTML を切り出す
                    $pattern = '/<a[^>]*href="[^"]*item\/'.$newItem->id.'[^"]*"[^>]*>(.*?)<\/a>/s';
                    preg_match($pattern, $html, $matches);

                    $cardHtml = $matches[1] ?? '';

                    $hasSold = str_contains($cardHtml, 'index-sold-text');

                    if ($newItem->isPurchased()) {
                        $this->assertTrue($hasSold, "Item {$newItem->id} は購入済みなのに Sold がありません");
                    }
                }
            }//$purchaseKind


            //mypage(購入ページ)に入る
            $response = $this->get(route('mypage',['page'=>'buy']));
            // ビューに渡された items を取得
            $viewItems = $response->viewData('items');

            if($purchaseKind === $this->purchaseViewCheckKind){
                //mypage(購入ページ)に入ったか確認
                $response->assertStatus(200);
                //登録した商品が追加されているか確認
                $this->assertTrue($viewItems->contains($randomItem));
            }//$purchaseKind
        }//$purchaseDenialMarker

        $response = $this->get(route('purchase.item_id',['item_id' => $randomItemId]));
        if($purchaseKind !== $this->purchaseUndefinedKind){
            $response->assertStatus(200);

            //ページ内に住所が書かれていることを確認
            $response->assertSee($nextAddress);

            //ページ内に郵便番号が書かれていることを確認
            $response->assertSee($nextPostcode);

            //ページ内に建物名が書かれていることを確認
            $response->assertSee($nextBuilding);

            //購入方法の変更が表示にも反映されていることを確認
            $response->assertSee($nextPurchaseMethodName);
        }

        return($response);
    }

}