@extends('layouts.view')

@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">

                        <div class="brand-logo text-center">
                            <h3 class="text-primary">My Library</h3>
                        </div>

                        <h4>Hello! let's get started</h4>
                        <h6 class="fw-light">Sign in to continue.</h6>

                        {{-- Error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="pt-3">
                            @csrf

                            <div class="form-group">
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-lg" 
                                       placeholder="Email"
                                       value="{{ old('email') }}" 
                                       required autofocus>
                            </div>

                            <div class="form-group">
                                <input type="password" 
                                       name="password" 
                                       class="form-control form-control-lg" 
                                       placeholder="Password"
                                       required>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-medium auth-form-btn">
                                    SIGN IN
                                </button>
                            </div>

                            <div class="my-2 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input">
                                    <label class="form-check-label text-muted">Remember me</label>
                                </div>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="auth-link text-black">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <div class="mb-2">
                                <a href="{{ url('/auth/google') }}" 
                                   class="btn btn-block btn-google auth-form-btn">
                                    <i class="mdi mdi-google me-2"></i>
                                    Sign in with Google
                                </a>
                            </div>

                            <div class="text-center mt-4 fw-light">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-primary">
                                    Create
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
