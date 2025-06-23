<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\GetRequest;
use App\Http\Requests\Post\SearchRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends ApiController
{
    public function index(GetRequest $request) {
        $post = Post::where('post_type_id', $request->post_type_id)
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->get();
        return $this->respondWith(
            PostResource::collection($post)
        );
    }

 public function show(Post $post) {
    return $this->respondWith(
        PostResource::make($post)
    );
}



    /**
     * Handles the process of liking a post.
     * @param Post $post The post to be liked.
     * @return \Illuminate\Http\JsonResponse Returns a JSON response indicating success or failure of the operation.
     * @throws \Illuminate\Auth\AuthenticationException If the user is not authenticated.
     */
    public function likePost(Post $post)
    {
        $user = Auth::user();

        // Check if the user has already liked the post
        if (!$post->likedByUsers()->where('user_id', $user->id)->exists()) {
            // If not, attach the user to the post's likedByUsers relationship
            $post->likedByUsers()->attach($user->id);
        }

        // Return a JSON response indicating success
        return $this->respondWith([], "Post liked successfully");
    }

    public function unlikePost(Post $post)
    {
        $user = Auth::user();

        if ($post->likedByUsers()->where('user_id', $user->id)->exists()) {
            $post->likedByUsers()->detach($user->id);
        }

        return $this->respondWith([], "Post unliked successfully");
    }

    public function search(SearchRequest $request)
    {
        return $this->respondWith(
            Post::where('title', 'LIKE', "%$request->q%")
                ->orWhere('content', 'LIKE', "%$request->q%")
                ->paginate()
        );
    }


    public function getall()
    {
        $posts = Post::all()->map(function ($post) {
            $post->image_url = $post->image ? Storage::disk('s3')->url($post->image) : null;
            return $post;
        });

        return response()->json(['data' => $posts]);
    }


        public function view($id)
    {
        $post = Post::findOrFail($id);
        $post->image_url = $post->image ? Storage::disk('s3')->url($post->image) : null;

        return response()->json(['data' => $post]);
    }

public function store(Request $request)
{
    $data = $request->all();
    $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('playlist', 's3');
        $data['image'] = Storage::disk('s3')->url($path); // Get full URL
    }

    $post = Post::create($data);

    return response()->json([
        'message' => 'Post created',
        'data'    => $post,
    ], 200, [], JSON_UNESCAPED_SLASHES);
}



public function update(Request $request, $id)
{
    $post = Post::findOrFail($id);
    $data = $request->all();
    $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

    if ($request->hasFile('image')) {
        if ($post->image) {
            // Extract the file path from the full URL to delete it
            $pathOnly = str_replace(Storage::disk('s3')->url(''), '', $post->image);
            Storage::disk('s3')->delete($pathOnly);
        }

        $path = $request->file('image')->store('playlist', 's3');
        $data['image'] = Storage::disk('s3')->url($path); // Get full URL
    }

    $post->update($data);

    return response()->json([
        'message' => 'Post updated',
        'data'    => $post,
    ], 200, [], JSON_UNESCAPED_SLASHES);
}



public function bulkStore(Request $request)
{
    $posts = $request->input('posts', []);

    foreach ($posts as $postData) {
        Post::create([
            'title'          => $postData['title']          ?? '',
            'language'       => $postData['language']       ?? '',
            'festival'       => $postData['festival']       ?? '',
            'date'           => $postData['date']           ?? now(),
            'author'         => $postData['author']         ?? '',
            'city'           => $postData['city']           ?? '',
            'data'           => $postData['data']           ?? '',
            'post_type_id'   => $postData['post_type_id']   ?? 0,
            'category_id'    => $postData['category_id']    ?? null,   // ← add this
            'image'          => $postData['image']          ?? '',
            'post_type'      => $postData['post_type']      ?? '',
            'playlist'       => $postData['playlist']       ?? '',
            'content'        => $postData['content']        ?? '',
            'content_2'      => $postData['content_2']      ?? '',
            'shloka_part'    => $postData['shloka_part']    ?? '',
            'shloka_chapter' => $postData['shloka_chapter'] ?? '',
            'is_featured'    => filter_var($postData['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    return response()->json(['message' => 'Posts imported successfully']);
}




    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->image) {
            Storage::disk('s3')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }




}
