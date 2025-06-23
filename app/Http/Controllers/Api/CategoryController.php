<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Category\GetRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends ApiController
{
    public function index(GetRequest $request)
    {
        $collection = Category::withCount('posts')
            ->where('post_type_id', $request->post_type_id)
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($collection),
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function get()
    {
        $categories = Category::with('postType')->get()->map(function ($cat) {
            return [
                ...$cat->toArray(),
                'image' => $cat->image
                    ? Storage::disk('s3')->url($cat->image)
                    : null,
            ];
        });

        return response()->json([
            'data' => $categories,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function show($id)
    {
        $category = Category::with('postType')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'data' => [
                ...$category->toArray(),
                'image' => $category->image
                    ? Storage::disk('s3')->url($category->image)
                    : null,
            ],
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    // CREATE
public function store(Request $request)
{
    $validated = $request->validate([
        'name'         => '|string|max:255',
        'slug'         => 'string|max:255',
        'post_type'    => '|string|max:255',
        'post_type_id' => '|integer',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'is_featured'  => 'nullable|boolean',
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('Category', 's3');
        $validated['image'] = Storage::disk('s3')->url($path); // store full URL
    }

    $category = Category::create($validated);

    return response()->json([
        'message' => 'Category created',
        'data'    => $category,
    ], 200, [], JSON_UNESCAPED_SLASHES);
}


    // UPDATE
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'slug'         => 'string|max:255',
            'post_type'    => 'string|max:255',
            'post_type_id' => 'sometimes|integer',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_featured'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('s3')->exists($category->image)) {
                Storage::disk('s3')->delete($category->image);
            }

            $path = $request->file('image')->store('Category', 's3');
            $validated['image'] = $path;
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated',
            'data'    => [
                ...$category->toArray(),
                'image' => $category->image
                    ? Storage::disk('s3')->url($category->image)
                    : null,
            ],
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    // DELETE
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if ($category->image && Storage::disk('s3')->exists($category->image)) {
            Storage::disk('s3')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200, [], JSON_UNESCAPED_SLASHES);
    }
}
