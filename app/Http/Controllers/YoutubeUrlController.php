<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YoutubeUrl;

class YoutubeUrlController extends Controller
{
    // Show all URLs
    public function index()
    {
        return YoutubeUrl::all();
    }

    // Store new URL
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $youtubeUrl = YoutubeUrl::create([
            'url' => $request->url
        ]);

        return response()->json($youtubeUrl, 201);
    }

    // Update URL
    public function update(Request $request, $id)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $youtubeUrl = YoutubeUrl::findOrFail($id);
        $youtubeUrl->url = $request->url;
        $youtubeUrl->save();

        return response()->json($youtubeUrl);
    }

    // Delete
    public function destroy($id)
    {
        $youtubeUrl = YoutubeUrl::findOrFail($id);
        $youtubeUrl->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
