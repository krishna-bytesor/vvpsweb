<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = [
        'text',
        'image',
    ];

    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'image_url',
       
    ];
    public function getImageUrlAttribute()
    {
        return Str::startsWith($this->image, "https") ? $this->image : asset('storage/' . $this->image);
    }
 
   
}
