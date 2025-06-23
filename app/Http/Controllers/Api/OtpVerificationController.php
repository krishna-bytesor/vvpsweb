<?php

namespace App\Http\Controllers\Api;

use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends ApiController
{
    public function verifyOtp(Request $request)
    {
        $otp = Otp::where('for',(Auth::user()->email))
            ->where('code', $request->otp)
            ->whereNull('verified_at')
            ->where('created_at', ">=", now()->subMinutes(10))
            ->first();

        if (!$otp) {
            return $this->respondWith([], "Invalid or Expired OTP", 422);
        }

        $otp->markAsVerified()->save();

        Auth::user()->update(['email_verified_at' => now()]);

        return $this->respondWith([], "Verification successful");
    }

    public function resendOtp(Request $request)
    {
        if (Auth::user()->email_verified_at != null)
        {
            return $this->respondWith([], "Email already verified", 422);
        }

        $otp = Otp::where('for', Auth::user()->email)
            ->where('created_at', ">=", now()->subMinutes(5))
            ->latest()
            ->first();
        if (!$otp) {
            Otp::create(['for' => Auth::user()->email]);
        } else {
            $otp->sendOnEmail();
        }
        return $this->respondWith([], "OTP Email sent successfully");
    }
}
