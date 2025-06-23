<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\JWT\IdTokenVerifier;

class AuthController extends ApiController
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->logUserIn($user);
    }
    public function register(RegisterRequest $request)
    {
        $input = $request->validated();
        $input['password'] = Hash::make($input['password']);

        $user = User::create(
            Arr::only($input, User::getFillables())
        );

        // Send an Email for OTP.
        Otp::create([
            'for' => $user->email,
        ]);

        $user->refresh();

        return $this->logUserIn($user);
    }

    public function socialLogin(SocialLoginRequest $request)
    {
        $payload = $this->verifyToken(
            $request->id_token
        );

        if (!$payload) {
            return $this->respondWith([], 'Invalid Login request. Verification Failed');
        }

        if (!array_key_exists('email', $payload)) {
            Log::info("Email not Found:");
            Log::info($payload);
            return $this->failedResponse([], 'Could not get the email address from provider');
        }

        $user = User::whereEmail($payload['email'])
            ->first();
        // User Exists. Send Login Response
        if ($user) {
            return $this->logUserIn($user);
        }

        // If not exists, Create new Account.
        $user = User::create([
            'name' => $payload['name'] ?? '',
            'email' => $payload['email'],
            'social_id' => $payload['user_id'],
            'social_provider' => $payload['firebase']['sign_in_provider'],
            'email_verified_at' => now(),
        ]);

        return $this->logUserIn($user);
    }

    private function logUserIn($user)
    {
        $token = $user->createToken('app')->plainTextToken;
        return $this->respondWith(['token' => $token, 'user' => UserResource::make($user)]);
    }

    private function verifyToken($id_token)
    {
        try{
            $projectId = config('services.firebase.project_id');
            $idVerifier = IdTokenVerifier::createWithProjectId($projectId);
            $token = $idVerifier->verifyIdToken($id_token);
            return $token->payload();
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public function logout(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->respondWith([]);
    }
}
