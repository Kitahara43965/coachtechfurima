<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public const UNDEFINED_INDEX_KIND = 0;
    public const INDEX_INDEX_KIND = 1;
    public const MYLIST_INDEX_KIND = 2;
    public const SOLD_GOODS_INDEX_KIND = 3;
    public const BOUGHT_GOODS_INDEX_KIND = 4;

    public function index(Request $request)
    {
        $mode = $request->query('tab', 'index');
        if($mode == 'index'){
            $indexKind = self::INDEX_INDEX_KIND;
        }else if($mode == 'mylist'){
            $indexKind = self::MYLIST_INDEX_KIND;
        }//$mode
        return $this->onSearch($request,$indexKind);
    }


    public function mypage(Request $request)
    {
        $mode = $request->query('page', 'sell');
        if($mode === 'sell'){
            $indexKind = self::SOLD_GOODS_INDEX_KIND;
        }else if($mode === 'buy'){
            $indexKind = self::BOUGHT_GOODS_INDEX_KIND;
        }

        return $this->onSearch($request,$indexKind);
    }


    public function onSearch($request, $indexKind)
    {
        $coachtechImageDirectory = BaseController::COACHTECH_IMAGE_DIRECTORY;
        $itemImageDirectory = BaseController::ITEM_IMAGE_DIRECTORY;
        $itemImagePrefix = BaseController::ITEM_IMAGE_PREFIX;
        $userImageDirectory = BaseController::USER_IMAGE_DIRECTORY;
        $userImagePrefix = BaseController::USER_IMAGE_PREFIX;
        $defaultProfileImageDirectory = BaseController::DEFAULT_PROFILE_IMAGE_DIRECTORY;
        $defaultProfileImageName = BaseController::DEFAULT_PROFILE_IMAGE_NAME;
        $trashImageDirectory = BaseController::TRASH_IMAGE_DIRECTORY;
        $trashImageName = BaseController::TRASH_IMAGE_NAME;
        $undefinedIndexKind = self::UNDEFINED_INDEX_KIND;
        $indexIndexKind = self::INDEX_INDEX_KIND;
        $mylistIndexKind = self::MYLIST_INDEX_KIND;
        $soldGoodsIndexKind = self::SOLD_GOODS_INDEX_KIND;
        $boughtGoodsIndexKind = self::BOUGHT_GOODS_INDEX_KIND;

        $isItemList = true;
        $isMultipleFunctionHeader = true;

        $defaultProfilePreviewUrl = asset('storage/'.$defaultProfileImageDirectory.'/'.$defaultProfileImageName);
        $trashPreviewUrl = asset('storage/'.$trashImageDirectory.'/'.$trashImageName);

        $authenticatedUser = Auth::user();
        if($authenticatedUser){
            $authenticatedUserImageName = $authenticatedUser->image;
            $isFilledWithProfile = $authenticatedUser->is_filled_with_profile;
        }else{
            $authenticatedUserImageName = null;
            $isFilledWithProfile = false;
        }


        $keyword = $request->input('keyword');

        if ($request->has('keyword')) {
            if ($keyword === '' || $keyword === null) {
                session()->forget('search_keyword');
            } else {
                session(['search_keyword' => $keyword]);
            }
        } else {
            $keyword = session('search_keyword', '');
        }

        if ($indexKind == $indexIndexKind) {
            $query = Item::with([
                'condition',
                'categories',
                'favoritedByUsers',
                'purchasedByUsers',
                'comments.user',
            ]);
            if (Auth::check()) {
                $query = $query->where(function ($query) {
                    $query->whereHas('usersByOwnership', function ($q) {
                    $q->where('user_id', '!=', Auth::id());
                    })
                    ->orDoesntHave('usersByOwnership');
                });
            }
        }else if ($indexKind == $mylistIndexKind) {
            if (Auth::check()) {
                $query = Auth::user()->favoriteItems();
            }else{
                $query = Item::query()->whereRaw('1 = 0');
            }
        }else if ($indexKind == $soldGoodsIndexKind) {
            if (Auth::check()) {
                $query = Auth::user()->ownedItems();
            }else{
                $query = Item::query()->whereRaw('1 = 0');
            }
        }else if ($indexKind == $boughtGoodsIndexKind) {
            if (Auth::check()) {
                $query = Auth::user()
                    ->purchasedItems()
                    ->wherePivot('purchase_quantity', '>=', 1);
            }else{
                $query = Item::query()->whereRaw('1 = 0');
            }
        }else{
            $query = Item::query()->whereRaw('1 = 0');
        }//$indexKind


        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->get();


        return view('index', compact(
            'coachtechImageDirectory',
            'itemImageDirectory',
            'itemImagePrefix',
            'userImageDirectory',
            'userImagePrefix',
            'undefinedIndexKind',
            'indexIndexKind',
            'mylistIndexKind',
            'soldGoodsIndexKind',
            'boughtGoodsIndexKind',

            'defaultProfilePreviewUrl',
            'trashPreviewUrl',
            'authenticatedUser',
            'authenticatedUserImageName',
            'isFilledWithProfile',
            'isItemList',
            'isMultipleFunctionHeader',
            'indexKind',
            'items',
        ));
    }

    
}
