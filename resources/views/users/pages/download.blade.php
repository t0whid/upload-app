@extends('users.layouts.master')
@section('title', 'Verify to Download')

@section('content')
<div class="hosting" style="margin: 40px auto; max-width: 600px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3>🔒 Verify Captcha to Reveal Download Links</h3>
        <p>Please complete the captcha below to continue.</p>

        <form action="{{ route('download.show', $slugs) }}" method="POST" class="mt-3">
            @csrf

            <div class="captcha-box mb-3">
                <img src="{{ route('download.captcha') }}" alt="Captcha Image" id="captchaImage" style="border-radius:6px; border:1px solid #ccc;">
            </div>

            <input type="text" name="captcha" placeholder="Enter Captcha" class="form-control mb-2" style="text-align:center;">
            @error('captcha')
                <div style="color:red; font-size:14px;">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-purple mt-3">✅ Verify</button>
        </form>
    </div>
</div>
@endsection
