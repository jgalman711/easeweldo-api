@extends('emails.layout')

@section('title', $title)

@section('content')
    <p>
        Dear {{ $user->first_name }},<br><br>

        Welcome to the team! We’re thrilled to have you at {{ $company->name }}. We know you’re going to be a valuable asset to our company and can’t wait to see what you accomplish.<br><br><br>
    </p>
    <div style="text-align: center; padding: 10px;">
        <p>Your temporary password:</p>
        <div style="text-align: center; background-color: #f4f4f4; padding: 10px; display: inline-block;">
            <p style="margin:0; font-size: 24px; font-weight: bold;">{{ $temporaryPassword }}</p>
        </div>
        <p style="font-size: 12px; color: #777;">This password is valid for 1 hour. Please go to your profile and change it upon login.</p>
    </div>
@endsection