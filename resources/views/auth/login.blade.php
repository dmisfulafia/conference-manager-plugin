@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="text-center mb-4">
    <h2 class="heading-font fw-bold text-academic-green">Welcome Back</h2>
    <p class="text-muted">Sign in to access your dashboard, conferences, and submissions.</p>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 shadow-sm" role="alert">
        {{ session('success') }}
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

<form action="{{ route('login') }}" method="POST">
    @csrf

    <!-- Email address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="user@example.com" required>
        </div>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        </div>
    </div>

    <!-- Remember Me -->
    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label text-muted" for="remember">Remember me on this device</label>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-fulafia btn-lg">Sign In</button>
    </div>

    <div class="text-center mt-3">
        <span class="text-muted">Don't have an account yet?</span> 
        <a href="{{ route('register') }}" class="text-fulafia-gold fw-bold text-decoration-none ms-1">Register here</a>
    </div>
</form>
@endsection
