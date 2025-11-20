<?php

namespace Tests\Feature\Traits;
use Tests\Feature\Traits\ProfileTrait;
use Tests\Feature\Traits\InitialValueTrait;
use Illuminate\Support\Facades\Auth;
use \App\Models\Item;
use Illuminate\Support\Str;

trait CommentTrait
{
    use ProfileTrait,InitialValueTrait;

    protected $commentUndefinedKind = 0;
    protected $commentOkKind = 1;
    protected $commentBeforeLoginKind = 2;
    protected $commentMissingDescriptionKind = 3;
    protected $commentLongDescriptionKind = 4;

    protected $commentEmpty = '';
    protected $commentDescription = 'この商品、安売りできますか?';

    protected $randomMaxItemStatus = 1;
    protected $allMaxItemStatus = 2;

    public function comment($commentKind)
    {

        if($commentKind === $this->commentOkKind){
            $response = $this->profile($this->profileUndefinedKind);
        }else if($commentKind === $this->commentBeforeLoginKind){
            $response = $this->initialValue($this->initialValueUndefinedKind);
        }else if($commentKind === $this->commentMissingDescriptionKind){
            $response = $this->profile($this->profileUndefinedKind);
        }else if($commentKind === $this->commentLongDescriptionKind){
            $response = $this->profile($this->profileUndefinedKind);
        }else{//$commentKind
            $response = $this->profile($this->profileUndefinedKind);
        }//$commentKind

        $longDescription = str_repeat('a', 256);

        $items = Item::all();
         
        if($items->isNotEmpty()){
            $randomItem = $items->random();
        }else{
            $randomItem = null;
        }

        if($commentKind === $this->commentOkKind){
            $itemStatus = $this->allMaxItemStatus;
        }else if($commentKind === $this->commentBeforeLoginKind){
            $itemStatus = $this->randomMaxItemStatus;
        }else if($commentKind === $this->commentMissingDescriptionKind){
            $itemStatus = $this->randomMaxItemStatus;
        }else if($commentKind === $this->commentLongDescriptionKind){
            $itemStatus = $this->randomMaxItemStatus;
        }else{//$commentKind
            $itemStatus = $this->allMaxItemStatus;
        }//$commentKind

        if($itemStatus === $this->randomMaxItemStatus){
             //1つitemをランダムに選びコメント
            $maxItemNumber = 1;
        }else if($itemStatus === $this->allMaxItemStatus){
             //全てitemを選びコメント
            $maxItemNumber = $items->count();
        }//$itemStatus

        if($maxItemNumber >= 1){
            for($itemNumber=1;$itemNumber<=$maxItemNumber;$itemNumber++){

                if($itemStatus === $this->randomMaxItemStatus){
                    $selectedItem = $randomItem;
                }else if($itemStatus === $this->allMaxItemStatus){
                    $selectedItem = $items[$itemNumber - 1];
                }//$itemStatus

                $selectedItemId = $selectedItem ? $selectedItem->id : null;

                if($commentKind !== $this->commentUndefinedKind){
                    //購入ページに入る
                    $response = $this->get(route('item.item_id', ['item_id' => $selectedItem->id]));
                    $response->assertStatus(200);
                }//$commentKind

                if($commentKind === $this->commentOkKind){
                    $tag = null;
                    $message = null;
                    $description = $this->commentDescription;
                }else if($commentKind === $this->commentBeforeLoginKind){
                    $tag = null;
                    $message = null;
                    $description = $this->commentDescription;
                }else if($commentKind === $this->commentMissingDescriptionKind){
                    $tag = 'description';
                    $message = 'コメントを入力してください';
                    $description = $this->commentEmpty;
                }else if($commentKind === $this->commentLongDescriptionKind){
                    $tag = 'description';
                    $message = 'コメントを255字以内で入力してください';
                    $description = $longDescription;
                }else{//$commentKind
                    $tag =  null;
                    $message = null;
                    $description = $this->commentDescription;
                }//$commentKind

                $dataForm = [
                    'description' => $description,
                ];

                $beforeCount = $selectedItem->comments()->count();

                $response = $this
                    ->post(route('item.item_id.comments',['item_id' => $selectedItemId]), $dataForm);

                $afterCount = $selectedItem->comments()->count();

                if($commentKind === $this->commentOkKind){
                    //コメントの数が増えたことを確認
                    $this->assertEquals($beforeCount + 1, $afterCount);
                }else if($commentKind === $this->commentBeforeLoginKind){
                    //コメント失敗
                    $response->assertStatus(302);
                    $response->assertRedirect(route('login'));
                }else if($commentKind === $this->commentMissingDescriptionKind){
                    $response->assertSessionHasErrors([
                        $tag => $message,
                    ]);
                }else if($commentKind === $this->commentLongDescriptionKind){
                    $response->assertSessionHasErrors([
                        $tag => $message,
                    ]);
                }//$commentKind
            }//$itemNumber
        }//$maxItemNumber

    }
}