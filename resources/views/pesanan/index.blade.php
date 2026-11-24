@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')
@section('judul', 'Riwayat Pesanan')

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
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Pesanan Saya</h3>
            <div class="card-tools">
                <a href="{{ route('katalog.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-book-open"></i> Belanja
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Kode Pesanan</th>
                            <th>Kantin</th>
                            <th class="text-center">Jumlah Menu</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                            <th>Waktu</th>
                            <th style="width: 90px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $pesanan)
                            <tr>
                                <td>{{ $pesanans->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $pesanan->kode_pesanan }}</strong></td>
                                <td>{{ $pesanan->kantin->nama_kantin ?? '-' }}</td>
                                <td class="text-center">{{ $pesanan->details_count }}</td>
                                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $warna[$pesanan->status] ?? 'secondary' }}">
                                        {{ ucfirst($pesanan->status) }}
                                    </span>
                                </td>
                                <td>{{ $pesanan->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada pesanan. Yuk pesan menu favoritmu di katalog.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer clearfix">
            {{ $pesanans->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection