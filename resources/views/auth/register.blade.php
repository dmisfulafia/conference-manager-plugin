@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="text-center mb-4">
    <img src="{{ asset('logo.png') }}" alt="FULafia Logo" class="mb-3" style="height: 80px; width: auto; object-fit: contain;">
    <h2 class="heading-font fw-bold text-academic-green">Register</h2>
    <p class="text-muted">Create an account to register for conferences, make payments, and submit papers.</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register') }}" method="POST">
    @csrf

    <div class="row">
        <!-- Title selection -->
        <div class="col-md-2 col-4 mb-3">
            <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <select name="title" id="title" class="form-select px-2" required>
                <option value="" disabled selected>Title</option>
                <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
            </select>
        </div>

        <!-- First Name -->
        <div class="col-md-5 col-8 mb-3">
            <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="First Name" required>
        </div>

        <!-- Last Name -->
        <div class="col-md-5 col-12 mb-3">
            <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Last Name" required>
        </div>
    </div>

    <div class="row">
        <!-- Other Names -->
        <div class="col-12 mb-3">
            <label for="other_names" class="form-label fw-semibold">Other Names <span class="text-muted">(Optional)</span></label>
            <input type="text" name="other_names" id="other_names" class="form-control" value="{{ old('other_names') }}" placeholder="Other Names (Middle Name)">
        </div>
    </div>

    <div class="row">
        <!-- Email address -->
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. user@example.com" required>
        </div>

        <!-- Phone number -->
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
            <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 080XXXXXXXX" required>
        </div>
    </div>

    <div class="row">
        <!-- Gender selection -->
        <div class="col-md-6 mb-3">
            <label for="gender" class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
            <select name="gender" id="gender" class="form-select" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <!-- Country selection -->
        <div class="col-md-6 mb-3">
            <label for="country" class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
            <select name="country" id="country" class="form-select" required>
                <option value="" disabled selected>Select Country</option>
                <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>United States</option>
                <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya</option>
                <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
    </div>

    <div class="row">
        <!-- Occupation -->
        <div class="col-md-6 mb-3">
            <label for="occupation" class="form-label fw-semibold">Occupation <span class="text-danger">*</span></label>
            <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation') }}" placeholder="e.g. Lecturer, Student" required>
        </div>

        <!-- Institution -->
        <div class="col-md-6 mb-3">
            <label for="institution" class="form-label fw-semibold">Institution <span class="text-muted">(Optional)</span></label>
            <input type="text" name="institution" id="institution" class="form-control" value="{{ old('institution') }}" placeholder="e.g. Federal University of Lafia">
        </div>
    </div>

    <!-- Password and Confirmation -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 8 characters" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter password" required>
        </div>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-fulafia btn-lg">Create Account</button>
    </div>

    <div class="text-center mt-3">
        <span class="text-muted">Already have an account?</span> 
        <a href="{{ route('login') }}" class="text-fulafia-gold fw-bold text-decoration-none ms-1">Login here</a>
    </div>
</form>
@endsection
