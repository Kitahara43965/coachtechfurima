<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','username','is_filled_with_profile','postcode','address','building','image'
    ];

    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    // 出品/所有商品
    public function ownedItems()
    {
        return $this->belongsToMany(Item::class, 'user_item')
                    ->wherePivot('type', 'ownership')
                    ->withTimestamps();
    }

    // お気に入り商品
    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'user_item')
                    ->wherePivot('type', 'favorite')
                    ->withTimestamps();
    }

    // 購入商品
    public function purchasedItems(){
        return $this->belongsToMany(Item::class, 'user_item')
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
}