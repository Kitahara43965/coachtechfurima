<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'user_id'];

    // コメントを書いたユーザー（多対一）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // コメントされた商品（多対多）
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_comment', 'comment_id', 'item_id')
                    ->withTimestamps();
    }
}
