<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'slug', 'post_type_id','post_type'];

    public function postType()
    {
        return $this->belongsTo(PostType::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
