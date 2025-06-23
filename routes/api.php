<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\OtpVerificationController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostTypeController;
use App\Http\Controllers\Api\TutorialController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\YoutubeUrlController;
use App\Http\Controllers\InstantChatController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);

    Route::post('verify-otp', [OtpVerificationController::class, 'verifyOtp'])->middleware('auth:sanctum');
    Route::post('resend-otp', [OtpVerificationController::class, 'resendOtp'])->middleware('auth:sanctum');

    Route::post('change-password/send-otp', [ChangePasswordController::class, 'sendOtp']);
    Route::post('change-password/verify-otp', [ChangePasswordController::class, 'verifyOtp']);
    Route::post('change-password/set', [ChangePasswordController::class, 'changePassword']);


    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('categories', [CategoryController::class, 'index']);

Route::get('categoriess', [CategoryController::class, 'get']);
Route::get('categories/{id}', [CategoryController::class, 'show']);
Route::post('categories', [CategoryController::class, 'store']);
Route::post('categories/{id}', [CategoryController::class, 'update']);
Route::delete('categories/{id}', [CategoryController::class, 'destroy']);


Route::get('post-types', [PostTypeController::class, 'index']);






Route::get('banners', [BannerController::class, 'index']);
Route::post('/banner-post', [BannerController::class, 'store']);           // POST new banner
Route::post('/banner-update/{id}', [BannerController::class, 'update']);
    // PUT update banner
Route::delete('/banner-delete/{id}', [BannerController::class, 'destroy']); 




Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [UserController::class, 'me']);
    Route::post('me', [UserController::class, 'saveProfile']);
    Route::delete('me', [UserController::class, 'deleteAccount']);
    Route::delete('delete-user-request', [UserController::class, 'deleteAccount']);

    Route::post('posts/{post}/like', [PostController::class, 'likePost']);
    Route::post('posts/{post}/unlike', [PostController::class, 'unlikePost']);

    Route::apiResource('posts/{post}/notes', NoteController::class);

    Route::get('search/posts', [PostController::class, 'search']);

    Route::get('user/liked-posts', [UserController::class, 'likedPost']);
    Route::get('user/notes', [UserController::class, 'notes']);

    Route::post('playlist/{playlist}/add/{post}', [PlaylistController::class, 'addAudio']);
    Route::post('playlist/{playlist}/remove/{post}', [PlaylistController::class, 'removeAudio']);

    Route::apiResource('playlist', PlaylistController::class);

    Route::get('my-chats', [InstantChatController::class, 'getChatRecords']);
    Route::post('initiate-chat', [InstantChatController::class, 'initiateChatRecord']);

    Route::apiResource('donations', DonationController::class)->only(['index', 'store']);
    Route::get('/tutorials', [TutorialController::class, 'index']);
});


Route::get('/posts/{post}', [PostController::class,'show'])
     ->where('post', '\d+');
Route::post('/posts/bulk', [PostController::class,'bulkStore']);

Route::apiResource('/posts', PostController::class)->only(['index', 'show']);

Route::get('/getposts', [PostController::class, 'getall']);
Route::post('/posts', [PostController::class, 'store']);
Route::post('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);


Route::get('/posts/{id}', [PostController::class, 'view'])->where('id', '[0-9]+');


Route::get('/projects',  [ProjectController::class, 'index']);
Route::post('/projects',   [ProjectController::class, 'store']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);
// Option B: use HTTP PUT and the correct placeholder
Route::post('/projects/{id}', [ProjectController::class, 'update']);


Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


Route::get('/public/users', [UserController::class, 'allUsers']);
Route::put('/public/users/{id}', [UserController::class, 'updateUser']); // PUT for update
Route::delete('/public/users/{id}', [UserController::class, 'deleteUser']);
Route::get('/public/users/{id}', [UserController::class, 'showUser']);
Route::post('/users', [UserController::class, 'createUser']);
Route::get('users/{user}/notes', [UserController::class, 'notes']);
Route::get('get/post-types', [PostTypeController::class, 'index']);


Route::get('/youtube-urls', [YoutubeUrlController::class, 'index']);
Route::post('/youtube-urls', [YoutubeUrlController::class, 'store']);
Route::post('/youtube-urls/{id}', [YoutubeUrlController::class, 'update']);
Route::delete('/youtube-urls/{id}', [YoutubeUrlController::class, 'destroy']);
Route::get('/youtube-urls/{id}', [YoutubeUrlController::class, 'show']);