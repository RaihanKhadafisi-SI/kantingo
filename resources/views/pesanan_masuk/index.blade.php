@extends('layouts.admin')

@section('title', 'Pesanan Masuk')
@section('judul', 'Pesanan Masuk')

@php
    $warna = [
        'menunggu'   => 'warning',
        'diproses'   => 'info',
        'siap'       => 'primary',
        'selesai'    => 'success',
        'dibatalkan' => 'danger',
    ];

    $statusList = ['menunggu', 'diproses', 'siap', 'selesai', 'dibatalkan'];
@endphp

@section('content')
    @if (! $kantin)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Akun Anda belum terhubung dengan kantin. Silakan hubungi admin untuk dibuatkan kantin terlebih dahulu.
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                Daftar Pesanan
                @if ($kantin)
                    <small class="text-muted">- {{ $kantin->nama_kantin }}</small>
                @endif
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('pesanan-masuk.index') }}" class="form-inline mb-3">
                <select name="status" class="form-control mr-2">
                    <option value="">Semua status</option>
                    @foreach ($statusList as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-info mr-1"><i class="fas fa-filter"></i> Saring</button>
                <a href="{{ route('pesanan-masuk.index') }}" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Kode Pesanan</th>
                            <th>Pembeli</th>
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
                                <td>{{ $pesanan->user->name ?? '-' }}</td>
                                <td class="text-center">{{ $pesanan->details_count }}</td>
                                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $warna[$pesanan->status] ?? 'secondary' }}">
                                        {{ ucfirst($pesanan->status) }}
                                    </span>
                                </td>
                                <td>{{ $pesanan->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pesanan-masuk.show', $pesanan) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada pesanan masuk.
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