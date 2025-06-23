<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\PostResource;
use App\Models\Playlist;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserNote;
use App\Models\InstantChat;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends ApiController
{
    public function likedPost()
    {
        $user = Auth::user();
        return $this->respondWith(
            PostResource::collection($user->likedPosts)
        );
    }

    public function notes()
    {
        $user = Auth::user();
        return $this->respondWith(
            $user->notes()->with('post')->get()
        );
    }

    public function me()
    {
        $user = Auth::user();
        return $this->respondWith($user);
    }

    public function saveProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $user->update(
            $request->validated()
        );

        return $this->respondWith(
            $user
        );
    }

    public function deleteAccount()
    {
        Playlist::where('user_id', Auth::id())->delete();
        PostLike::where('user_id', Auth::id())->delete();
        UserNote::where('user_id', Auth::id())->delete();
        InstantChat::where('user_id', Auth::id())->delete();

        User::where('id', Auth::id())->delete();

        return $this->respondWith(
            [], 'Account deleted successfully'
        );




    }



 public function allUsers()
    {
        return response()->json(['data' => User::all()]);
    }

    public function allUsersFromAllSources(FirebaseService $firebase)
    {
        return response()->json([
            'laravel_users' => User::all(),
            'firebase_auth_users' => $firebase->getAuthUsers(),
            'realtime_db_users' => $firebase->getRealtimeUsers(), // ✅
        ]);
    }

    public function showUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['data' => $user]);
    }

    public function updateUser(Request $request, $id, FirebaseService $firebase)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'is_swami' => 'nullable|boolean',
            'is_disciple' => 'nullable|boolean',
        ]);

        $user->update($validated);

        if ($user->firebase_uid) {
            $firebase->updateFirebaseUser($user->firebase_uid, $validated);
        }

        $firebase->syncUser($user->id, $user->toArray()); // ✅ Realtime DB update

        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    public function deleteUser($id, FirebaseService $firebase)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        Playlist::where('id', $user->id)->delete();
        PostLike::where('id', $user->id)->delete();
        UserNote::where('id', $user->id)->delete();
        InstantChat::where('id', $user->id)->delete();

        if ($user->firebase_uid) {
            $firebase->deleteFirebaseUser($user->firebase_uid);
        }

        $firebase->deleteUser($user->id); // ✅ Realtime DB delete
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function createUser(Request $request, FirebaseService $firebase)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $firebaseUid = $firebase->createFirebaseUser($data);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'firebase_uid' => $firebaseUid,
        ]);

        $firebase->syncUser($user->id, $user->toArray()); // ✅ Realtime DB create

        return response()->json(['message' => 'User created in DB + Firebase', 'data' => $user]);
    }

    public function syncFromFirebase(Request $request)
    {
        $data = $request->validate([
            'firebase_uid' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $user = User::updateOrCreate(
            ['firebase_uid' => $data['firebase_uid']],
            [
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => bcrypt('default_password'),
            ]
        );

        return response()->json(['message' => 'User synced from Firebase', 'data' => $user]);
    }


}
