
@component('mail::message')
# Email Verification OTP

An OTP request was raised for {{ $email }}, please verify it by using the below OTP.

<h2>{{ $otp }}</h2>

Please do not disclose this otp to anyone.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
