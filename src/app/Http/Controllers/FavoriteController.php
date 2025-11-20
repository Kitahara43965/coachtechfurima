<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function toggle(Request $request, $item_id)
    {
        $item  = Item::findOrFail($item_id);

        if (!$request->user()) {
            return redirect()->route('login')->with('status', 'ログインしてください');
        }
        
        $user = $request->user();

        // 既にお気に入り済みかチェック
        $isFavorited = $item->isFavoritedBy($user);

        if ($isFavorited) {
            // お気に入り解除
            $user->favoriteItems()
                 ->wherePivot('type', 'favorite')
                 ->detach($item->id);
        } else {
            // お気に入り登録
            $user->favoriteItems()
                 ->attach($item->id, ['type' => 'favorite']);
        }

        return back()->with('status', $isFavorited ? 'お気に入り解除しました' : 'お気に入りに追加しました');
    }
}
