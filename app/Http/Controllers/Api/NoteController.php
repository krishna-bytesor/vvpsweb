<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\UserNote;
use Illuminate\Http\Request;

class NoteController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return $this->respondWith(
            $post->notes
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'note' => 'required|max:255',
        ]);

        return $this->respondWith(
            $post->notes()->create([
                'user_id' => auth()->id(),
                'note' => $request->note,
            ])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, UserNote $note)
    {
        return $this->respondWith(
            $note
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, UserNote $note)
    {
        $request->validate([
            'note' => 'required|max:255',
        ]);

        $note->update([
            'note' => $request->note,
        ]);

        return $this->respondWith(
            $note
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, UserNote $note)
    {
        $note->delete();

        return $this->respondWith([], "Note deleted successfully");
    }
}
