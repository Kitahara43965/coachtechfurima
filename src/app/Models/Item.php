<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'condition_id','name','brand','description','price','image'
    ];

    // ユーザーとの関係（出品/お気に入り/購入）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category')
                    ->withTimestamps();
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function usersByOwnership()
    {
        return $this->belongsToMany(User::class, 'user_item')
                    ->wherePivot('type', 'ownership')
                    ->withTimestamps();
    }

    public function isOwnedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->usersByOwnership()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_item')
                    ->wherePivot('type', 'favorite')
                    ->withTimestamps();
    }

    public function purchasedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_item')
                    ->wherePivot('type', 'purchase')
                    ->withPivot([
                        'type',
                        'purchase_quantity',
                        'price_at_purchase',
                        'purchased_at',
                        'purchase_method_id',
                        'is_filled_with_delivery_address',
                        'delivery_postcode',
                        'delivery_address',
                        'delivery_building',
                    ])
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'item_comment', 'item_id', 'comment_id')
                    ->withTimestamps();
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false; // 未ログイン時は false を返す
        }

        // user_item テーブルから「type = favorite」で存在確認
        return $this->favoritedByUsers()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    public function favoritesCount(): int
    {
        return $this->favoritedByUsers()->count();
    }

    public function pivotFor(User $user, string $type = 'purchase')
    {
        return $this->belongsToMany(User::class, 'user_item')
                    ->wherePivot('type', $type)
                    ->where('users.id', $user->id)
                    ->first()?->pivot;
    }

    public function isPurchased(): bool
    {
        return $this->purchasedByUsers()
                    ->wherePivot('purchase_quantity', '>=', 1)
                    ->exists();
    }

    public function isPurchasedBy(User $user): bool
    {
        if(!$user){
            return false;
        }

        return $this->purchasedByUsers()
                    ->where('user_id', $user->id)
                    ->wherePivot('purchase_quantity', '>=', 1)
                    ->exists();
    }
    

}

