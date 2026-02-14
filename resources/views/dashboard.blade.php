@extends('layout.main')

@section('title', 'Dashboard')

@section('content')
    <h2>Dashboard</h2>
    <p>Selamat datang, {{ auth()->user()->name }}</p>

    <div class="row">

    {{-- CARD KATEGORI --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <h4 class="card-title">Kategori</h4>
                <p class="card-text">Kelola data kategori buku</p>
                <a href="{{ route('kategori.index') }}" class="btn btn-light btn-sm">
                    Lihat Kategori
                </a>
            </div>
        </div>
    </div>

    {{-- CARD BUKU --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <h4 class="card-title">Buku</h4>
                <p class="card-text">Kelola koleksi buku</p>
                <a href="{{ route('buku.index') }}" class="btn btn-light btn-sm">
                    Lihat Buku
                </a>
            </div>
        </div>
    </div>

</div>
@endsection


