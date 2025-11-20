<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\PurchaseMethod;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class DisplayController extends Controller
{
    public const UNDEFINED_DISPLAY_KIND = 0;
    public const ADD_DISPLAY_KIND = 1;
    public const EDIT_DISPLAY_KIND = 2;
    public const EVALUATE_DISPLAY_KIND = 3;
    public const PURCHASE_DISPLAY_KIND = 4;
    public const ADDRESS_DISPLAY_KIND = 5;
    public const PROFILE_DISPLAY_KIND = 6;

    public function add($item_id = null) {
        $displayKind = self::ADD_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function edit($item_id = null){
        $displayKind = self::EDIT_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function evaluate($item_id = null){
        $displayKind = self::EVALUATE_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function purchase($item_id = null){
        $displayKind = self::PURCHASE_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function address($item_id = null){
        $displayKind = self::ADDRESS_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function profile($item_id = null){
        $displayKind = self::PROFILE_DISPLAY_KIND;
        return $this->onCreate($displayKind,$item_id);
    }

    public function onCreate($displayKind,$item_id){

        $coachtechImageDirectory = BaseController::COACHTECH_IMAGE_DIRECTORY;
        $itemImageDirectory = BaseController::ITEM_IMAGE_DIRECTORY;
        $itemImagePrefix = BaseController::ITEM_IMAGE_PREFIX;
        $userImageDirectory = BaseController::USER_IMAGE_DIRECTORY;
        $defaultProfileImageDirectory = BaseController::DEFAULT_PROFILE_IMAGE_DIRECTORY;
        $defaultProfileImageName = BaseController::DEFAULT_PROFILE_IMAGE_NAME;
        $trashImageDirectory = BaseController::TRASH_IMAGE_DIRECTORY;
        $trashImageName = BaseController::TRASH_IMAGE_NAME;
        $userImagePrefix = BaseController::USER_IMAGE_PREFIX;
        $undefinedDisplayKind = self::UNDEFINED_DISPLAY_KIND;
        $addDisplayKind = self::ADD_DISPLAY_KIND;
        $editDisplayKind = self::EDIT_DISPLAY_KIND;
        $evaluateDisplayKind = self::EVALUATE_DISPLAY_KIND;
        $purchaseDisplayKind = self::PURCHASE_DISPLAY_KIND;
        $addressDisplayKind = self::ADDRESS_DISPLAY_KIND;
        $profileDisplayKind = self::PROFILE_DISPLAY_KIND;

        $isItemList = false;
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

        $categories = Category::all();
        $conditions = Condition::all();
        $purchaseMethods = PurchaseMethod::all();

        if($displayKind === $addDisplayKind){
            $returnedViewFile = 'sell';
            $selectedItemMakeMarker = 0;
        }else if($displayKind === $editDisplayKind){
            $returnedViewFile = 'sell';
            $selectedItemMakeMarker = 1;
        }else if($displayKind === $evaluateDisplayKind){
            $returnedViewFile = 'evaluation';
            $selectedItemMakeMarker = 2;
        }else if($displayKind === $purchaseDisplayKind){
            $returnedViewFile = 'purchase';
            $selectedItemMakeMarker = 3;
        }else if($displayKind === $addressDisplayKind){
            $returnedViewFile = 'address';
            $selectedItemMakeMarker = 4;
        }else if($displayKind === $profileDisplayKind){
            $returnedViewFile = 'profile';
            $selectedItemMakeMarker = 0;
        }else{//$displayKind
            $returnedViewFile = 'sell';
            $selectedItemMakeMarker = 0;
        }//$displayKind

        if($selectedItemMakeMarker == 0){
            $selectedItem = null;
        }else{
            if($item_id){
                $selectedItem = Item::findOrFail($item_id);
            }else{
                $selectedItem = null;
            }
        }

        $authenticatedUserIdCoincidence = false;
        $authenticatedUserId = Auth::id();
        
        if($selectedItem){
            $selectedUserIds = $selectedItem->usersByOwnership->pluck('id')->toArray();
            $selectedCategoryIds = $selectedItem->categories->pluck('id')->toArray();
            $selectedConditionId = $selectedItem->condition->id ?? null;
            $isPurchased = $selectedItem->isPurchased();
            if($selectedItem->comments){
                $selectedItemCommentNumber = $selectedItem->comments->count();
            }else{
                $selectedItemCommentNumber = 0;
            }
            $owners = $selectedItem->usersByOwnership;
            
            if ($authenticatedUser) {
                $selectedPurchaseTypedPivot = $authenticatedUser->purchasedItems()
                    ->wherePivot('item_id', $item_id)
                    ->wherePivot('type', 'purchase')
                    ->first();

                $isOwner = $owners->contains($authenticatedUserId);
                $isPurchasedBy = $selectedItem->isPurchasedBy($authenticatedUser);
                $selectedPurchaseMethodId = optional(
                    $selectedItem->purchasedByUsers->firstWhere('id', $authenticatedUserId)
                )->pivot->purchase_method_id ?? null;
            }else{
                $selectedPurchaseTypedPivot = null;
                $isOwner = false;
                $isPurchasedBy = false;
                $selectedPurchaseMethodId = null;
            }

            $selectedFavoritedUsers = $selectedItem->favoritedByUsers;
            $selectedCommentDescriptions = $selectedItem->comments->pluck('description')->toArray();
            $selectedCategories = Category::whereIn('id', $selectedCategoryIds)->get();
            $selectedCondition = Condition::findOrFail($selectedConditionId);

        }else{
            
            $selectedUserIds = null;
            $selectedCategoryIds = null;
            $selectedConditionId = null;
            $isPurchased = false;
            $selectedItemCommentNumber = 0;
            $owners = null;
            $selectedPurchaseTypedPivot = null;
            $isOwner = false;
            $isPurchasedBy = false;
            $selectedPurchaseMethodId = null;
            $selectedFavoritedUsers = null;
            $selectedCommentDescriptions = null;
            $selectedCategories = null;
            $selectedCondition = null;
        }
        
        if($selectedUserIds != null){
            if (in_array($authenticatedUserId, $selectedUserIds)) {
                $authenticatedUserIdCoincidence = true;
            }
        }//$selectedUserIds

        return view($returnedViewFile,compact(
            'itemImageDirectory',
            'itemImagePrefix',
			'userImageDirectory',
            'userImagePrefix',
            'coachtechImageDirectory',
            'undefinedDisplayKind',
            'addDisplayKind',
            'editDisplayKind',
            'evaluateDisplayKind',
            'purchaseDisplayKind',
            'addressDisplayKind',
            'profileDisplayKind',

            'displayKind',
            'isItemList',
            'isMultipleFunctionHeader',
            'defaultProfilePreviewUrl',
            'trashPreviewUrl',
            'authenticatedUser',
            'authenticatedUserImageName',
            'isFilledWithProfile',
            'authenticatedUserIdCoincidence',
            'item_id',
            'categories',
            'conditions',
            'purchaseMethods',
            'selectedItem',
            'selectedCategoryIds',
            'selectedConditionId',
            'isPurchased',
            'selectedItemCommentNumber',
            'isPurchasedBy',
            'isOwner',
            'selectedPurchaseMethodId',
            'selectedPurchaseTypedPivot',
            'selectedFavoritedUsers',
            'selectedCommentDescriptions',
            'selectedCategories',
            'selectedCondition',
        ));
        
    }//onCreate
}
