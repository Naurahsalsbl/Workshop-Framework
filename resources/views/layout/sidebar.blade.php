       <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-image">
                  <img src="{{ asset('assets/images/faces/face2.jpg') }}" alt="profile" />
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2">Naurahsalsbl</span>
                  <span class="text-secondary text-small">Project Manager</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('kategori.index') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-view-list menu-icon"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('buku.index') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book menu-icon"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('sertifikat') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('/sertifikat') }}">
                <span class="menu-title">Generate Sertifikat</span>
                <i class="mdi mdi-file-document menu-icon"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('pengumuman') ? 'active' : '' }}">
              <a class="nav-link" href="{{ url('/pengumuman') }}">
                <span class="menu-title">Generate Pengumuman</span>
                <i class="mdi mdi-file-document menu-icon"></i>
              </a>
            </li>
            <li class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Tag Harga (Barang)</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#tugas-js" aria-expanded="false" aria-controls="tugas-js">
                <span class="menu-title">Tugas Javascript</span>
                <i class="menu-arrow"></i>
              </a>

              <div class="collapse" id="tugas-js">
                <ul class="nav flex-column sub-menu">

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tugas.barang') ? 'active' : '' }}"
                  href="{{ route('tugas.barang') }}">
                  Form Barang
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tugas.html') ? 'active' : '' }}"
                  href="{{ route('tugas.html') }}">
                  Table HTML Biasa
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tugas.datatable') ? 'active' : '' }}"
                  href="{{ route('tugas.datatable') }}">
                  Table DataTables
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tugas.kota') ? 'active' : '' }}"
                  href="{{ route('tugas.kota') }}">
                  Kota
                </a>
              </li>
            </ul>
          </div>
        </li>
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('wilayah.index') ? 'active' : '' }}"
                  href="{{ route('wilayah.index') }}">
                  Wilayah
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}"
                  href="{{ route('pos.index') }}">
                  POS / Kasir
                </a>
              </li>
      </ul>
    </nav>