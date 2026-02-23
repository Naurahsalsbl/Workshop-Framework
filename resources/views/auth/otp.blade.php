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

                        <h4>Verifikasi OTP</h4>
                        <h6 class="fw-light">
                            Masukkan kode OTP yang dikirim ke email kamu.
                        </h6>

                        {{-- Success Message --}}
                        @if(session('message'))
                            <div class="alert alert-success mt-3">
                                {{ session('message') }}
                            </div>
                        @endif

                        {{-- Error Message --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('otp.verify') }}" class="pt-3">
                            @csrf

                            <div class="form-group">
                                <input type="text"
                                       name="otp"
                                       class="form-control form-control-lg text-center"
                                       placeholder="Masukkan 6 digit OTP"
                                       maxlength="6"
                                       required>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                <button type="submit"
                                        class="btn btn-primary btn-lg font-weight-medium auth-form-btn">
                                    VERIFIKASI
                                </button>
                            </div>

                            <div class="text-center mt-4 fw-light">
                                Belum menerima kode?
                                <a href="{{ route('login') }}" class="text-primary">
                                    Login ulang
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
