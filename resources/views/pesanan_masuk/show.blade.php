@extends('layouts.admin')

@section('title', 'Detail Pesanan Masuk')
@section('judul', 'Detail Pesanan Masuk')

@php
    $warna = [
        'menunggu'   => 'warning',
        'diproses'   => 'info',
        'siap'       => 'primary',
        'selesai'    => 'success',
        'dibatalkan' => 'danger',
    ];

    $tombolBerikutnya = [
        'menunggu' => ['label' => 'Terima dan Proses Pesanan', 'ikon' => 'fa-fire', 'kelas' => 'btn-info'],
        'diproses' => ['label' => 'Tandai Siap Diambil', 'ikon' => 'fa-bell', 'kelas' => 'btn-primary'],
        'siap'     => ['label' => 'Tandai Selesai', 'ikon' => 'fa-check-double', 'kelas' => 'btn-success'],
    ];

    $aksi = $tombolBerikutnya[$pesanan->status] ?? null;
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
                                    <th colspan="4" class="text-right">Total</th>
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

                    <p class="mb-1 text-muted small">Pembeli</p>
                    <p><i class="fas fa-user"></i> {{ $pesanan->user->name ?? '-' }}</p>

                    <p class="mb-1 text-muted small">Waktu Pesan</p>
                    <p>{{ $pesanan->created_at->format('d M Y, H:i') }}</p>

                    <p class="mb-1 text-muted small">Catatan dari Pembeli</p>
                    <p class="mb-0">{{ $pesanan->catatan ?: '-' }}</p>
                </div>
                <div class="card-footer">
                    @if ($aksi)
                        <form action="{{ route('pesanan-masuk.status', $pesanan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn {{ $aksi['kelas'] }} btn-block">
                                <i class="fas {{ $aksi['ikon'] }}"></i> {{ $aksi['label'] }}
                            </button>
                        </form>
                    @endif

                    @if (in_array($pesanan->status, ['menunggu', 'diproses']))
                        <form action="{{ route('pesanan-masuk.batal', $pesanan) }}" method="POST"
                              onsubmit="return confirm('Batalkan pesanan ini? Stok menu akan dikembalikan.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-times"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('pesanan-masuk.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection