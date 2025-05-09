@extends('base::email.layout')

@section('content')
    <p>Hi {{$user->name}},</p>

    <p>You are receiving this email because we received a password reset request for your account.</p>

    <p>This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.</p>

    <p>If you did not request a password reset, no further action is required.</p>

    <p>
        <a href="{{ $redirectUrl }}" style="background:#e64a14;color:#FFFFFF;padding:0.5rem" target="_blank">RESET PASSWORD</a>
    </p>
@endsection
