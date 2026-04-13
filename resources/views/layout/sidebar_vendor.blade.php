<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav flex-column p-0">
    {{-- Vendor Menu --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" href="{{ route('vendor.dashboard') }}">
        <i class="bi bi-speedometer2 me-2"></i> Vendor Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('vendor.menu.*') ? 'active' : '' }}" href="{{ route('vendor.menu.index') }}">
        <i class="bi bi-card-list me-2"></i> Menu Vendor
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('vendor.pesanan.*') ? 'active' : '' }}" href="{{ route('vendor.pesanan.index') }}">
        <i class="bi bi-basket me-2"></i> Pesanan Vendor
      </a>
    </li>

    <hr class="my-2">

    {{-- Customer Pemesanan --}}
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('customer.order.*') ? 'active' : '' }}" href="{{ route('customer.order.index') }}">
        <i class="bi bi-cart me-2"></i> Pemesanan Customer
      </a>
    </li>

  </ul>
</nav>