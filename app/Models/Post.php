<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends BaseModel
{
    use HasFactory;

        protected $fillable = [
        'title', 'content', 'content_2', 'language', 'festival',
        'image', 'post_type', 'category', 'playlist', 'date', 'is_featured', 'author',
        'country', 'city', 'data', 'shloka_part', 'shloka_chapter', 'type','post_type_id','category_id',
        'url_type', 'description'
    ];

    protected $casts = [
       
    ];

    public function postLikes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(UserNote::class);
    }

    // public function images()
    // {
    //     return $this->hasMany(PostImage::class);
    // }
}
