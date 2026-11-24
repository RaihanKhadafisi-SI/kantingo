@extends('layouts.admin')

@section('title', 'Keranjang')
@section('judul', 'Keranjang Saya')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Isi Keranjang</h3>
            <div class="card-tools">
                <a href="{{ route('katalog.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Lanjut Belanja
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if (count($items) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px">No</th>
                                <th style="width: 90px">Foto</th>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th style="width: 180px">Jumlah</th>
                                <th>Subtotal</th>
                                <th style="width: 70px" class="text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        @if ($item->menu->foto)
                                            <img src="{{ asset('storage/' . $item->menu->foto) }}" alt="Foto"
                                                 style="width:70px;height:55px;object-fit:cover;border-radius:6px;">
                                        @else
                                            <span class="text-muted"><i class="fas fa-hamburger fa-2x"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->menu->nama_menu }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-store"></i> {{ $item->menu->kantin->nama_kantin ?? '-' }}
                                        </small>
                                    </td>
                                    <td>Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('keranjang.ubah', $item->menu) }}" method="POST" class="form-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="jumlah" value="{{ $item->jumlah }}"
                                                   min="1" max="{{ $item->menu->stok }}"
                                                   class="form-control form-control-sm mr-1" style="width:70px;">
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="fas fa-sync-alt"></i> Ubah
                                            </button>
                                        </form>
                                    </td>
                                    <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                                    <td class="text-center">
                                        <form action="{{ route('keranjang.hapus', $item->menu) }}" method="POST"
                                              onsubmit="return confirm('Hapus {{ $item->menu->nama_menu }} dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="5" class="text-right">Total</th>
                                <th colspan="2" class="text-success" style="font-size: 1.2rem;">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                    <p class="mb-3">Keranjang masih kosong.</p>
                    <a href="{{ route('katalog.index') }}" class="btn btn-primary">
                        <i class="fas fa-book-open"></i> Lihat Katalog Menu
                    </a>
                </div>
            @endif
        </div>

        @if (count($items) > 0)
            <div class="card-footer d-flex justify-content-between">
                <form action="{{ route('keranjang.kosongkan') }}" method="POST"
                      onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt"></i> Kosongkan Keranjang
                    </button>
                </form>

                @if (Route::has('pesanan.buat'))
                    <a href="{{ route('pesanan.buat') }}" class="btn btn-success">
                        <i class="fas fa-check"></i> Buat Pesanan
                    </a>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="fas fa-check"></i> Buat Pesanan
                    </button>
                @endif
            </div>
        @endif
    </div>
@endsection