<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = ['title', 'images'];

protected $casts = [
    'images' => 'array',
];
    protected $appends = ['image_urls'];

    public function getImageUrlsAttribute(): array
    {
        return collect($this->images ?? [])
            ->map(fn($path) => Storage::disk('s3')->url($path))
            ->all();
    }
}
