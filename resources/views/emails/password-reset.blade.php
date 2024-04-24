@extends('emails.layouts.guest')

@section('content')
<div>
    <h2>Password Reset</h2>
    <p>We received a request to reset your password. Click the button below to reset it:</p>
    <p><a href="{{ $resetLink }}" class="button">Reset Password</a></p>
    <p>If you did not request a password reset, you can ignore this email.</p>
</div>
@endsection
