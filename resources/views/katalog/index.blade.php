@extends('layouts.admin')

@section('title', 'Katalog Menu')
@section('judul', 'Katalog Menu')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-body">
            <form method="GET" action="{{ route('katalog.index') }}" class="form-inline">
                <input type="text" name="cari" value="{{ request('cari') }}"
                       class="form-control mr-2 mb-2" placeholder="Cari nama menu...">

                <select name="kategori_id" class="form-control mr-2 mb-2">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <select name="kantin_id" class="form-control mr-2 mb-2">
                    <option value="">Semua kantin</option>
                    @foreach ($kantins as $kantin)
                        <option value="{{ $kantin->id }}" {{ request('kantin_id') == $kantin->id ? 'selected' : '' }}>
                            {{ $kantin->nama_kantin }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-info mr-1 mb-2">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="{{ route('katalog.index') }}" class="btn btn-secondary mb-2">Reset</a>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse ($menus as $menu)
            <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                <div class="card w-100">
                    @if ($menu->foto)
                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}"
                             class="card-img-top" style="height:160px;object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted"
                             style="height:160px;">
                            <i class="fas fa-hamburger fa-4x"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title font-weight-bold mb-1">{{ $menu->nama_menu }}</h5>
                        <small class="text-muted mb-2">
                            <i class="fas fa-store"></i> {{ $menu->kantin->nama_kantin ?? '-' }}
                            &middot; {{ $menu->kategori->nama_kategori ?? '-' }}
                        </small>

                        @if ($menu->deskripsi)
                            <p class="card-text small text-muted mb-2">{{ $menu->deskripsi }}</p>
                        @endif

                        <div class="mt-auto">
                            <h5 class="text-success font-weight-bold mb-1">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted d-block mb-2">Stok: {{ $menu->stok }}</small>

                            @if (Route::has('keranjang.tambah'))
                                <form action="{{ route('keranjang.tambah') }}" method="POST" class="form-inline">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                    <input type="number" name="jumlah" value="1" min="1" max="{{ $menu->stok }}"
                                           class="form-control form-control-sm mr-2" style="width:70px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i> Tambah
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-cart-plus"></i> Tambah
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Belum ada menu yang tersedia.
                </div>
            </div>
        @endforelse
    </div>

    <div class="clearfix">
        {{ $menus->links('pagination::bootstrap-4') }}
    </div>
@endsection