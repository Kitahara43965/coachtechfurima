<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Traits\CommentTrait;
use Tests\Feature\Traits\ProfileTrait;
use \App\Models\Item;
use \App\Models\PurchaseMethod;
use App\Http\Controllers\BaseController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


trait EvaluationTrait
{
    use CommentTrait,ProfileTrait;

    protected $evaluationUndefinedKind = 0;
    protected $evaluationCheckKind = 1;
    protected $evaluationCategoryCheckKind = 2;


    public function evaluation($evaluationKind){

        $response = $this->comment($this->commentUndefinedKind);

        if($evaluationKind == $this->evaluationCategoryCheckKind){
            //カテゴリーが複数あるものを選択
            $availableItems = Item::has('categories', '>=', 2)->has('comments', '>=', 1)->get();
        }else{//$evaluationKind
            //全てのアイテムを選択
            $availableItems = Item::has('comments', '>=', 1)->get();
        }//$evaluationKind
        $randomItem = $availableItems->random();
        $randomItemId = $randomItem->id;

        $isDefault = $randomItem->is_default;

        if($isDefault){
            $randomItemImagePath = $this->coachtechImageDirectory.'/'.$randomItem->image;
        }else{
            $randomItemImagePath = $this->itemImageDirectory.'/'.$randomItem->image;
        }
        // item のページにアクセス（存在確認）
        $response = $this->get(route('item.item_id', ['item_id' => $randomItemId]));

        if($evaluationKind !== $this->evaluationUndefinedKind){
            //ページがあるか確認
            $response->assertStatus(200);

            //選択されたカテゴリーが全て表示されているか確認
            foreach ($randomItem->categories as $category) {
                $response->assertSee($category->name);
            }

        }//$evaluationKind


        if($evaluationKind === $this->evaluationCheckKind){

            

            //プロフィール画像がフォルダーに存在するか確認
            Storage::disk('public')->assertExists($randomItemImagePath);

            //商品画像を確認
            $randomItemPreviewUrl = asset('storage/' . $randomItemImagePath);
            $response->assertSee($randomItemPreviewUrl, false);

            //商品名が表示されているか確認
            $response->assertSee($randomItem->name);

            if(!is_null($randomItem->brand)){
                //ブランド名が表示されているか確認
                $response->assertSee($randomItem->brand);
            }//$randomItem->brand

            //価格が表示されているか確認
            $response->assertSee(number_format($randomItem->price));

            //いいねの数が表示されているか確認
            $response->assertSee($randomItem->favoritesCount());

            //コメント数が表示されているか確認
            $response->assertSee($randomItem->comments->count());

            //商品説明が表示されているか確認
            $response->assertSee($randomItem->description,false);

            //状態が表示されているか確認
            $response->assertSee($randomItem->condition->name);

            foreach($randomItem->comments as $comment){

                $candidateUserImage = $comment->user->image;
                if($candidateUserImage){
                    $newUserImage = $candidateUserImage;
                    $newUserImagePath = $this->profileUserImageDirectory.'/'.$newUserImage;
                    $newUserPreviewUrl = asset('storage/'.$newUserImagePath);
                }else{//$candidateUserImage
                    $newUserImage = null;
                    $newUserImagePath = null;
                    $newUserPreviewUrl = null;
                }//$candidateUserImage

                if($newUserImage){
                    //プロフィール画像が登録されている場合、ファイルが表示されているか確認
                    Storage::disk('public')->assertExists($newUserImagePath);
                    $response->assertSee($newUserPreviewUrl, false);
                }//$newUserImage

                //ユーザー名が表示されているか確認
                $response->assertSee($comment->user->username);

                //コメントの内容が表示されているか確認
                $response->assertSee($comment->description, false);

            }

        }//$evaluationKind


        return($response);
    }

}