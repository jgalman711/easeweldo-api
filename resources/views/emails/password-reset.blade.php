@extends('emails.layout')

@section('content')
<div class="bg-white max-w-lg mx-auto p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-semibold mb-6">Password Reset</h1>
    <p class="mt-4">You are receiving this email because we received a password reset request for your account.</p>
    <p class="my-8 text-center">
        <a href="" class="w-full bg-blue-800 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Reset Password
        </a>
    </p>
    <p class="mt-4">This password reset link will expire in 60 minutes.</p>
    <p class="mt-4 text-sm">If you didn't request a password reset, you can ignore this email.</p>
</div>
@endsection
