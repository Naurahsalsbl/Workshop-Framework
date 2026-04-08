@extends('layouts.view')

@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5" style="background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">

                        {{-- Logo / Brand --}}
                        <div class="brand-logo text-center mb-3">
                            <div style="width:60px; height:60px; background: linear-gradient(135deg,#667eea,#764ba2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                <i class="mdi mdi-store text-white" style="font-size:28px;"></i>
                            </div>
                        </div>

                        <h4 class="text-center mb-1">Login Vendor</h4>
                        <h6 class="fw-light text-center mb-4">Masuk ke dashboard vendor kantin</h6>

                        {{-- Error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('vendor.login.post') }}" class="pt-3">
                            @csrf

                            <div class="form-group mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-account text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           name="username" 
                                           class="form-control border-start-0 ps-0 form-control-lg" 
                                           placeholder="Username"
                                           value="{{ old('username') }}" 
                                           required autofocus>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           name="password" 
                                           id="passwordInput"
                                           class="form-control border-start-0 border-end-0 ps-0 form-control-lg" 
                                           placeholder="Password"
                                           required>
                                    <span class="input-group-text bg-light border-start-0" style="cursor:pointer;" onclick="togglePassword()">
                                        <i class="mdi mdi-eye text-muted" id="eyeIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                <button type="submit" class="btn btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                    <i class="mdi mdi-login me-2"></i>Masuk
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('mdi-eye', 'mdi-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('mdi-eye-off', 'mdi-eye');
        }
    }
</script>
@endsection