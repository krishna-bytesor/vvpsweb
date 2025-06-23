<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Playlist\DeleteRequest;
use App\Http\Requests\Playlist\StoreRequest;
use App\Http\Requests\Playlist\UpdateRequest;
use App\Models\Playlist;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->respondWith(
            $user->playlists
        );
    }

    public function show(Playlist $playlist)
    {
        $playlist->append('audios');
        return $this->respondWith(
            $playlist
        );
    }

    public function store(StoreRequest $request)
    {
        return $this->respondWith(
            Playlist::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'audio' => []
            ])
        );
    }

    public function update(UpdateRequest $request, Playlist $playlist)
    {
        $playlist->update([
            'name' => $request->name,
        ]);
        return $this->respondWith(
            $playlist
        );
    }

    public function destroy(DeleteRequest $request, Playlist $playlist)
    {
        $playlist->delete();
        return $this->respondWith([], "Playlist deleted successfully");
    }

    public function addAudio(Playlist $playlist, Post $post)
    {
        if ($playlist->user_id != Auth::id()) {
            abort(403);
        }
        $audio = $playlist->audio;
        array_push($audio, $post->id);
        $playlist->audio = array_unique($audio);
        $playlist->save();
        return $this->respondWith(
            $playlist,
            "Audio added to playlist"
        );
    }

    public function removeAudio(Playlist $playlist, Post $post)
    {
        if ($playlist->user_id != Auth::id()) {
            abort(403);
        }
        $audio = $playlist->audio;
        if (array_search($post->id, $audio) !== false) {
            unset($audio[array_search($post->id, $audio)]);
            $playlist->audio = array_unique(array_values($audio));
            $playlist->save();
        }
        return $this->respondWith(
            $playlist,
            "Audio removed from playlist"
        );
    }
}
