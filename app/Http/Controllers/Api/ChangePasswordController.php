<?php

namespace App\Http\Controllers\Api;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends ApiController
{
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required'
        ]);

        $otp = Otp::where('for',($request->email))
            ->where('code', $request->otp)
            ->whereNull('verified_at')
            ->where('created_at', ">=", now()->subMinutes(10))
            ->first();

        if (!$otp) {
            return $this->respondWith([], "Invalid or Expired OTP", 422);
        }

        $otp->markAsVerified()->save();

        return $this->respondWith([], "Verification successful");
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otp = Otp::where('for', $request->email)
            ->where('created_at', ">=", now()->subMinutes(5))
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            Otp::create(['for' => $request->email]);
        } else {
            $otp->sendOnEmail();
        }
        return $this->respondWith([], "OTP Email sent successfully");
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'otp' => 'required'
        ]);

        $otp = Otp::where('for', $request->email)
            ->where('code', $request->otp)
            ->whereNotNull('verified_at')
            ->where('created_at', ">=", now()->subMinutes(10))
            ->first();


        if (!$otp) {
            return $this->respondWith([], "Invalid or Expired OTP", 422);
        }
        DB::beginTransaction();

        $otp->delete();
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::commit();

        return $this->respondWith([], "Password changed successfully");
    }
}
