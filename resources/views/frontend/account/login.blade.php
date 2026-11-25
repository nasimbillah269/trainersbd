@extends('frontend.layouts.app')

@section('title', 'Student Login | TrainersBD')
@section('body_class', 'page-account')
@push('css')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<section class="section account-auth">
    <div class="container">
        <div class="account-auth__card panel">
            <span class="account-auth__icon"><i class="bi bi-mortarboard"></i></span>
            <h1 class="account-auth__title">Student login</h1>
            <p class="account-auth__lead">Log in to see the courses you've bought and watch your lessons.</p>

            <form method="post" action="{{ route('customer.login.post') }}" novalidate>
                @csrf
                @error('login')
                    <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label class="form-label" for="loginId">Mobile number or email</label>
                    <div class="input-icon"><i class="bi bi-person"></i>
                        <input type="text" class="form-control" id="loginId" name="login" value="{{ old('login') }}" placeholder="01XXXXXXXXX" required autocomplete="username" autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="loginPass">Password</label>
                    <div class="input-icon"><i class="bi bi-key"></i>
                        <input type="password" class="form-control" id="loginPass" name="password" required autocomplete="current-password">
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="loginRemember" name="remember" value="1">
                    <label class="form-check-label" for="loginRemember">Keep me logged in</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Log in</button>
            </form>

            <div class="account-auth__foot">
                <p>Don't have an account yet? It's created automatically when you buy your first course at checkout.</p>
                <p>Forgot your password? Contact us and we'll help you get back in.</p>
            </div>
        </div>
    </div>
</section>
@endsection
