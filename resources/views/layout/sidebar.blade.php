<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav flex-column p-0">

    {{-- Profile --}}
    <li class="nav-item nav-profile mb-3">
      <a href="#" class="nav-link d-flex align-items-center">
        <div class="nav-profile-image me-2">
          <img src="{{ asset('assets/images/faces/face2.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold">Naurahsalsbl</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge ms-auto"></i>
      </a>
    </li>

    {{-- Main Dashboard --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    {{-- Kategori --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-view-list menu-icon"></i>
      </a>
    </li>

    {{-- Buku --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}" href="{{ route('buku.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book menu-icon"></i>
      </a>
    </li>

    {{-- Sertifikat & Pengumuman --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('sertifikat') ? 'active' : '' }}" href="{{ url('/sertifikat') }}">
        <span class="menu-title">Generate Sertifikat</span>
        <i class="mdi mdi-file-document menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('pengumuman') ? 'active' : '' }}" href="{{ url('/pengumuman') }}">
        <span class="menu-title">Generate Pengumuman</span>
        <i class="mdi mdi-file-document menu-icon"></i>
      </a>
    </li>

    {{-- Barang --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">
        <span class="menu-title">Tag Harga (Barang)</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>

    {{-- Tugas JS Collapse --}}
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#tugas-js" aria-expanded="false">
        <span class="menu-title">Tugas Javascript</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="tugas-js">
        <ul class="nav flex-column sub-menu ps-3">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('tugas.barang') ? 'active' : '' }}" href="{{ route('tugas.barang') }}">
              Form Barang
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('tugas.html') ? 'active' : '' }}" href="{{ route('tugas.html') }}">
              Table HTML Biasa
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('tugas.datatable') ? 'active' : '' }}" href="{{ route('tugas.datatable') }}">
              Table DataTables
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('tugas.kota') ? 'active' : '' }}" href="{{ route('tugas.kota') }}">
              Kota
            </a>
          </li>
        </ul>
      </div>
    </li>

    {{-- Wilayah --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('wilayah.index') ? 'active' : '' }}" href="{{ route('wilayah.index') }}">
        Wilayah
      </a>
    </li>

    {{-- POS / Kasir --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}" href="{{ route('pos.index') }}">
        POS / Kasir
      </a>
    </li>

    <hr class="my-2">

    {{-- Vendor Menu --}}
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#vendor-list" aria-expanded="false">
            <span class="menu-title">Vendor</span>
            <i class="mdi mdi-store menu-icon"></i>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="vendor-list">
            <ul class="nav flex-column sub-menu ps-3">
                @foreach($vendors as $vendor)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vendor.masuk', $vendor->idvendor) }}">
                        <i class="mdi mdi-store-outline me-1"></i> {{ $vendor->nama_vendor }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </li>
    
    <hr class="my-2">

    

  </ul>
</nav>