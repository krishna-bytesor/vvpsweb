<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use Illuminate\Http\Request;

class PostTypeController extends ApiController
{
    public function index()
    {
        return $this->respondWith(PostType::get());
    }
}
