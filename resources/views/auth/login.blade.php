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

                        {{-- Tab --}}
                        <ul class="nav nav-tabs mt-3" id="loginTab">
                            <li class="nav-item">
                                <a class="nav-link {{ !session('active_tab') || session('active_tab') == 'admin' ? 'active' : '' }}"
                                    href="#" onclick="switchTab('admin')">
                                    <i class="mdi mdi-account me-1"></i>Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session('active_tab') == 'vendor' ? 'active' : '' }}"
                                    href="#" onclick="switchTab('vendor')">
                                    <i class="mdi mdi-store me-1"></i>Vendor
                                </a>
                            </li>
                        </ul>

                        {{-- Tab Admin --}}
                        <div id="tabAdmin" class="pt-3">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email"
                                        class="form-control form-control-lg"
                                        placeholder="Email"
                                        value="{{ old('email') }}" required autofocus>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password"
                                        class="form-control form-control-lg"
                                        placeholder="Password" required>
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
                                        <i class="mdi mdi-google me-2"></i>Sign in with Google
                                    </a>
                                </div>
                                <div class="text-center mt-4 fw-light">
                                    Don't have an account?
                                    <a href="{{ route('register') }}" class="text-primary">Create</a>
                                </div>
                            </form>
                        </div>

                        {{-- Tab Vendor --}}
                        <div id="tabVendor" class="pt-3" style="display:none">
                            <form method="POST" action="{{ route('vendor.login.post') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="username"
                                        class="form-control form-control-lg"
                                        placeholder="Username Vendor"
                                        value="{{ old('username') }}" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password"
                                        class="form-control form-control-lg"
                                        placeholder="Password" required>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit" class="btn btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        <i class="mdi mdi-store me-2"></i>LOGIN VENDOR
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Cek tab aktif dari session (kalau ada error vendor)
    @if(session('active_tab') == 'vendor' || $errors->has('login'))
    switchTab('vendor');
    @endif

    function switchTab(tab) {
        document.getElementById('tabAdmin').style.display = tab === 'admin' ? 'block' : 'none';
        document.getElementById('tabVendor').style.display = tab === 'vendor' ? 'block' : 'none';

        document.querySelectorAll('#loginTab .nav-link').forEach((el, i) => {
            el.classList.remove('active');
            if ((tab === 'admin' && i === 0) || (tab === 'vendor' && i === 1)) {
                el.classList.add('active');
            }
        });
    }
</script>
@endsection