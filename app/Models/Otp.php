<?php

namespace App\Models;

use App\Mail\OtpEmail;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function Illuminate\Events\queueable;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'for',
        'code',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime'
    ];


    public static function booted()
    {
        static::creating(function (Otp $otp) {
            $otp->code = self::generateOtp($otp->for);
        });

        static::created(queueable(function (Otp $otp) {
            $otp->sendOnEmail();
        }));
    }

    public function sendOnEmail() {
        Mail::send(new OtpEmail($this->for, $this->code));
    }

    public static function generateOtp($for = null)
    {
        return rand(1000, 9999);
    }

    public function markAsVerified()
    {
        $this->verified_at = now();
        return $this;
    }
}
