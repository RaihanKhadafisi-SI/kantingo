<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Kantin Go</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Navbar atas --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    {{-- Sidebar --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link text-center">
            <i class="fas fa-utensils text-warning"></i>
            <span class="brand-text font-weight-bold">Kantin Go</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="{{ route('profile.edit') }}" class="d-block">{{ auth()->user()->name }}</a>
                    <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @if (auth()->user()->isAdmin())
                        <li class="nav-header">MANAJEMEN KANTIN</li>

                        <li class="nav-item">
                            <a href="{{ route('kantin.index') }}"
                               class="nav-link {{ request()->routeIs('kantin.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-store"></i>
                                <p>Data Kantin</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('kategori.index') }}"
                               class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Data Kategori</p>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isAdmin() || auth()->user()->isPenjual())
                        <li class="nav-header">{{ auth()->user()->isAdmin() ? 'MANAJEMEN MENU' : 'KANTIN SAYA' }}</li>

                        <li class="nav-item">
                            <a href="{{ route('menu.index') }}"
                               class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>Data Menu</p>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isPenjual())
                        <li class="nav-item">
                            <a href="{{ route('pesanan-masuk.index') }}"
                               class="nav-link {{ request()->routeIs('pesanan-masuk.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Pesanan Masuk</p>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'pembeli')
                        <li class="nav-header">BELANJA</li>

                        <li class="nav-item">
                            <a href="{{ route('katalog.index') }}"
                               class="nav-link {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Katalog Menu</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('keranjang.index') }}"
                               class="nav-link {{ request()->routeIs('keranjang.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Keranjang
                                    @if (count(session('keranjang', [])) > 0)
                                        <span class="badge badge-warning right">{{ count(session('keranjang', [])) }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('pesanan.index') }}"
                               class="nav-link {{ request()->routeIs('pesanan.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Pesanan Saya</p>
                            </a>
                        </li>
                    @endif

                </ul>
            </nav>
        </div>
    </aside>

    {{-- Isi halaman --}}
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('judul', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('judul', 'Dashboard')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>Kantin Go</strong> &copy; {{ date('Y') }} - Tugas Startup Digital
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>