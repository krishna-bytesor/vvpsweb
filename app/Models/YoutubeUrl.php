<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeUrl extends Model
{
    use HasFactory;

    protected $fillable = ['url']; // Allow mass-assignment for 'url'
}
