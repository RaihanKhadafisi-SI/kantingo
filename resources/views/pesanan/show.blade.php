@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('judul', 'Detail Pesanan')

@php
    $warna = [
        'menunggu'   => 'warning',
        'diproses'   => 'info',
        'siap'       => 'primary',
        'selesai'    => 'success',
        'dibatalkan' => 'danger',
    ];
@endphp

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Isi Pesanan</h3>
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
                                @foreach ($pesanan->details as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $detail->nama_menu }}</strong></td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $detail->jumlah }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="4" class="text-right">Total Bayar</th>
                                    <th class="text-right text-success" style="font-size: 1.2rem;">
                                        Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
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
                    <h3 class="card-title">Informasi Pesanan</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1 text-muted small">Kode Pesanan</p>
                    <h5 class="font-weight-bold">{{ $pesanan->kode_pesanan }}</h5>

                    <p class="mb-1 text-muted small">Status</p>
                    <p>
                        <span class="badge badge-{{ $warna[$pesanan->status] ?? 'secondary' }}"
                              style="font-size: 0.95rem;">
                            {{ ucfirst($pesanan->status) }}
                        </span>
                    </p>

                    <p class="mb-1 text-muted small">Kantin</p>
                    <p><i class="fas fa-store"></i> {{ $pesanan->kantin->nama_kantin ?? '-' }}</p>

                    <p class="mb-1 text-muted small">Waktu Pesan</p>
                    <p>{{ $pesanan->created_at->format('d M Y, H:i') }}</p>

                    <p class="mb-1 text-muted small">Catatan</p>
                    <p class="mb-0">{{ $pesanan->catatan ?: '-' }}</p>
                </div>
                <div class="card-footer">
                    @if ($pesanan->status === 'menunggu')
                        <form action="{{ route('pesanan.batalkan', $pesanan) }}" method="POST"
                              onsubmit="return confirm('Batalkan pesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-times"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-list"></i> Riwayat Pesanan
                    </a>
                    <a href="{{ route('katalog.index') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-book-open"></i> Belanja Lagi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection