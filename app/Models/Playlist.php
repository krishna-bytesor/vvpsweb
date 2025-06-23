<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'audio'];

    protected $casts = [
        "audio" => "array",
    ];

    protected $appends = [
        "item_count"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getItemCountAttribute()
    {
        return count($this->audio ?? []);
    }

    public function getAudiosAttribute()
    {
        return Post::whereIn('id', $this->audio)->get();
    }
}
