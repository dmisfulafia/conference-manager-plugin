@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="text-center mb-4">
    <div class="mb-3">
        <i class="bi bi-shield-check text-fulafia-gold" style="font-size: 3.5rem;"></i>
    </div>
    <h2 class="heading-font fw-bold text-academic-green">Verify Your Email</h2>
    <p class="text-muted">We have sent a verification code to your registered email address. Please enter the 6-digit OTP code below to verify your account.</p>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 shadow-sm" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('verification.verify') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label for="code" class="form-label fw-semibold text-center d-block fs-5">6-Digit Verification Code</label>
        <input type="text" name="code" id="code" class="form-control form-control-lg text-center fs-3 fw-bold letter-spacing-lg" 
               placeholder="XXXXXX" maxlength="6" pattern="\d{6}" required autocomplete="off">
        <div class="form-text text-center mt-2">Enter the exact numeric code sent to your email inbox/spam folder.</div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-fulafia btn-lg">Verify Account</button>
    </div>
</form>

<div class="mt-4 text-center">
    <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
        @csrf
        <span class="text-muted">Didn't receive the code?</span>
        <button type="submit" class="btn btn-link text-fulafia-gold fw-bold p-0 border-0 align-baseline text-decoration-none ms-1">Resend Code</button>
    </form>
</div>

<div class="mt-3 text-center">
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-secondary small text-decoration-none">
        <i class="bi bi-box-arrow-left"></i> Log Out
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

<style>
    .letter-spacing-lg {
        letter-spacing: 0.5rem;
    }
</style>
@endsection
