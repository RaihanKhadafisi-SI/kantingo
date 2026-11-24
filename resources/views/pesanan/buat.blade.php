@extends('layouts.admin')

@section('title', 'Konfirmasi Pesanan')
@section('judul', 'Konfirmasi Pesanan')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pesanan.simpan') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            Ringkasan Pesanan
                            <small class="text-muted">
                                - <i class="fas fa-store"></i> {{ $kantin->nama_kantin ?? '-' }}
                            </small>
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px">No</th>
                                        <th>Menu</th>
                                        <th>Harga</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $item->menu->nama_menu }}</strong></td>
                                            <td>Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->jumlah }}</td>
                                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="4" class="text-right">Total Bayar</th>
                                        <th class="text-right text-success" style="font-size: 1.2rem;">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Catatan untuk Penjual</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <textarea name="catatan" class="form-control" rows="4" maxlength="255"
                                      placeholder="Contoh: tidak pakai pedas (boleh kosong)">{{ old('catatan') }}</textarea>
                            <small class="text-muted">Maksimal 255 karakter.</small>
                        </div>

                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle"></i>
                            Pesanan akan dikirim ke penjual dan berstatus <strong>Menunggu</strong>.
                        </p>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Pesan Sekarang
                        </button>
                        <a href="{{ route('keranjang.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection