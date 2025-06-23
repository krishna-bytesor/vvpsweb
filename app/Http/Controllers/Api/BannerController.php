<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends ApiController
{
    public function index()
    {
        return response()->json([
            'data' => Banner::all()
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $storedPath = $request->file('image')->store('banners', 's3');
        $fullUrl = Storage::disk('s3')->url($storedPath); // get full URL

        $banner = Banner::create([
            'text' => $validated['text'],
            'image' => $fullUrl, // store full URL in DB
        ]);

        return response()->json([
            'message' => 'Banner created successfully.',
            'data' => $banner
        ], 201, [], JSON_UNESCAPED_SLASHES);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'text' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Extract path from full URL to delete the old image
            if ($banner->image) {
                $oldPath = parse_url($banner->image, PHP_URL_PATH);
                $oldPath = ltrim($oldPath, '/'); // remove leading slash
                if (Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }
            }

            $newPath = $request->file('image')->store('banners', 's3');
            $banner->image = Storage::disk('s3')->url($newPath); // store full URL
        }

        if (isset($validated['text'])) {
            $banner->text = $validated['text'];
        }

        $banner->save();

        return response()->json([
            'message' => 'Banner updated successfully.',
            'data' => $banner
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image) {
            $path = parse_url($banner->image, PHP_URL_PATH);
            $path = ltrim($path, '/');
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        $banner->delete();

        return response()->json([
            'message' => 'Banner deleted successfully.'
        ], Response::HTTP_OK, [], JSON_UNESCAPED_SLASHES);
    }
}
