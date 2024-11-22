@component('mail::message')
# Hello!

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => route('reset_password.index', ['token' => $data['token'], 'email' => $data['email']])])
    Reset Password
@endcomponent

This password reset link will expire in {{ $data['expired_minutes'] }} minutes.
If you did not request a password reset, no further action is required

Regards,<br>
{{ config('app.name') }}
@endcomponent
