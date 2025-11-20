<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\PurchaseMethod;
use App\Http\Requests\SellRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class SellController
{
    

    public function countImages()
    {
        $baseController = new BaseController;
        $count = $baseController->getPublicImageNumber(BaseController::ITEM_IMAGE_DIRECTORY,BaseController::ITEM_IMAGE_PREFIX);
        return response()->json(['count' => $count]);
    }

    public function store(SellRequest $request){
        $item_id = null;
        return $this->onManage($request,$item_id);
    }//store

    public function update(SellRequest $request,$item_id){
        return $this->onManage($request,$item_id);
    }//store

    public function delete(Request $request,$item_id){
        return $this->onManage($request,$item_id);
    }//store

    public function onManage(Request $request,$item_id){

        
        $sellType = $request->input('sellType','');
        

        $baseController = new BaseController;
        $authenticatedUser = Auth::user();
        $previewUrl = null;
        $imageName = null;
        $requestCategoryId = $request->input('category_id', []); // デフォルト空配列

        if($sellType === 'createSellType'){
            $imageChangeMarker = 1;
            $returnedRoute = route('mypage');
        }else if($sellType === 'updateSellType'){
            $imageChangeMarker = 2;
            $returnedRoute = route('item.item_id', ['item_id' => $item_id]);
        }else if($sellType === 'deleteSellType'){
            $imageChangeMarker = 0;
            $returnedRoute = route('mypage');
        }else{
            $imageChangeMarker = 0;
            $returnedRoute = route('mypage');
        }//$sellType


        if($imageChangeMarker != 0){
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $originalImageName = $file->getClientOriginalName();
                $extension = pathinfo($originalImageName, PATHINFO_EXTENSION);

                $count = $baseController->getPublicImageNumber(BaseController::ITEM_IMAGE_DIRECTORY,BaseController::ITEM_IMAGE_PREFIX);
                $imageName = BaseController::ITEM_IMAGE_PREFIX . ($count + 1) . '.' . $extension;

                // 保存
                $path = $file->storeAs(BaseController::ITEM_IMAGE_DIRECTORY, $imageName, 'public');
                $previewUrl = asset('storage/'.$path);
            } else {
                $imageName = $request->input('image_name');
                $previewUrl = $request->input('preview_url');
            }
        }//$imageChangeMarker&0

        if($sellType === 'createSellType'){

            $item = DB::transaction(function () use ($request, $requestCategoryId, $authenticatedUser, $imageName) {
                $item = Item::create([
                    'name' => $request->name,
                    'brand' => $request->brand,
                    'description' => $request->description,
                    'price' => $request->price,
                    'image' => $imageName,
                    'condition_id' => $request->condition_id,
                ]);

                $authenticatedUser->ownedItems()->syncWithoutDetaching([
                    $item->id => ['type' => 'ownership']
                ]);

                

                if (!is_array($requestCategoryId)) {
                    // カンマ区切りの文字列を配列に変換
                    $categoryIds = array_map('intval', array_filter(explode(',', $requestCategoryId)));
                } else {
                    // 配列なら整数化して安全に
                    $categoryIds = array_map('intval', $requestCategoryId);
                }

                // sync は常に配列で渡す
                $item->categories()->sync($categoryIds);

                return($item);
            });

            return redirect($returnedRoute)
                ->with('imageMessage', '登録が完了しました！');

        }else if($sellType === 'updateSellType'){

            $item = DB::transaction(function () use ($item_id, $request, $requestCategoryId, $authenticatedUser,$imageName) {
                
                $item = Item::findOrFail($item_id);
                $item->update([
                    'name' => $request->name,
                    'brand' => $request->brand,
                    'description' => $request->description,
                    'price' => $request->price,
                    'image' => $imageName,
                    'condition_id' => $request->condition_id,
                ]);

                $authenticatedUser->ownedItems()->syncWithoutDetaching([
                    $item->id => ['type' => 'ownership']
                ]);

                if (!is_array($requestCategoryId)) {
                    // カンマ区切りの文字列を配列に変換
                    $categoryIds = array_map('intval', array_filter(explode(',', $requestCategoryId)));
                } else {
                    // 配列なら整数化して安全に
                    $categoryIds = array_map('intval', $requestCategoryId);
                }

                // sync は常に配列で渡す
                $item->categories()->sync($categoryIds);

                return($item);
            });

            return redirect($returnedRoute)
                ->with('imageMessage', '更新が完了しました！');
            
        }else if($sellType === 'deleteSellType'){

            $item = Item::with('comments')->findOrFail($item_id);

            foreach ($item->comments as $comment) {
                $comment->delete();
            }
            $item->comments()->detach();
            $item->categories()->detach();
            $item->usersByOwnership()->detach();
            $item->delete();

            return redirect($returnedRoute)->with('imageMessage', '削除が完了しました！');

        }else{//$sellType
            
            return redirect($returnedRoute)->with('imageMessage', '戻りました！');

        }//$sellType
    }
}