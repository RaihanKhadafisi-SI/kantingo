@extends('layouts.admin')

@section('title', 'Data Kantin')
@section('judul', 'Data Kantin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Data belum bisa disimpan:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Daftar Kantin</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Kantin
                </button>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('kantin.index') }}" class="form-inline mb-3">
                <input type="text" name="cari" value="{{ request('cari') }}"
                       class="form-control mr-2" placeholder="Cari nama atau lokasi...">
                <button type="submit" class="btn btn-info mr-1"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('kantin.index') }}" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">No</th>
                            <th style="width: 90px">Foto</th>
                            <th>Nama Kantin</th>
                            <th>Lokasi</th>
                            <th>Penjual</th>
                            <th>Jam Operasional</th>
                            <th class="text-center">Status</th>
                            <th style="width: 130px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kantins as $kantin)
                            <tr>
                                <td>{{ $kantins->firstItem() + $loop->index }}</td>
                                <td class="text-center">
                                    @if ($kantin->foto)
                                        <img src="{{ asset('storage/' . $kantin->foto) }}" alt="Foto"
                                             style="width:70px;height:55px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <span class="text-muted"><i class="fas fa-store fa-2x"></i></span>
                                    @endif
                                </td>
                                <td><strong>{{ $kantin->nama_kantin }}</strong></td>
                                <td>{{ $kantin->lokasi }}</td>
                                <td>{{ $kantin->penjual->name ?? '-' }}</td>
                                <td>{{ substr($kantin->jam_buka, 0, 5) }} - {{ substr($kantin->jam_tutup, 0, 5) }}</td>
                                <td class="text-center">
                                    @if ($kantin->status === 'buka')
                                        <span class="badge badge-success">Buka</span>
                                    @else
                                        <span class="badge badge-secondary">Tutup</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm"
                                            data-toggle="modal" data-target="#modalEdit{{ $kantin->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('kantin.destroy', $kantin) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus kantin {{ $kantin->nama_kantin }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="modalEdit{{ $kantin->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('kantin.update', $kantin) }}" method="POST"
                                          enctype="multipart/form-data" class="modal-content">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Kantin</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Nama Kantin</label>
                                                    <input type="text" name="nama_kantin" class="form-control"
                                                           value="{{ $kantin->nama_kantin }}" required>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Penjual (Pemilik)</label>
                                                    <select name="user_id" class="form-control" required>
                                                        @foreach ($penjuals as $penjual)
                                                            <option value="{{ $penjual->id }}"
                                                                {{ $kantin->user_id == $penjual->id ? 'selected' : '' }}>
                                                                {{ $penjual->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Lokasi</label>
                                                <input type="text" name="lokasi" class="form-control"
                                                       value="{{ $kantin->lokasi }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $kantin->deskripsi }}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>Jam Buka</label>
                                                    <input type="time" name="jam_buka" class="form-control"
                                                           value="{{ substr($kantin->jam_buka, 0, 5) }}" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Jam Tutup</label>
                                                    <input type="time" name="jam_tutup" class="form-control"
                                                           value="{{ substr($kantin->jam_tutup, 0, 5) }}" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="buka" {{ $kantin->status === 'buka' ? 'selected' : '' }}>Buka</option>
                                                        <option value="tutup" {{ $kantin->status === 'tutup' ? 'selected' : '' }}>Tutup</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Foto Kantin (kosongkan jika tidak diganti)</label>
                                                <input type="file" name="foto" class="form-control-file" accept="image/*">
                                                <small class="text-muted">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data kantin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer clearfix">
            {{ $kantins->links('pagination::bootstrap-4') }}
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('kantin.store') }}" method="POST"
                  enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kantin</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Kantin</label>
                            <input type="text" name="nama_kantin" class="form-control"
                                   placeholder="Contoh: Kantin Bu Rina" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Penjual (Pemilik)</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Pilih penjual --</option>
                                @foreach ($penjuals as $penjual)
                                    <option value="{{ $penjual->id }}">{{ $penjual->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="Contoh: Kantin Pusat, Gedung A lantai 1" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"
                                  placeholder="Keterangan singkat (boleh kosong)"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Jam Buka</label>
                            <input type="time" name="jam_buka" class="form-control" value="07:00" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Jam Tutup</label>
                            <input type="time" name="jam_tutup" class="form-control" value="16:00" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Kantin</label>
                        <input type="file" name="foto" class="form-control-file" accept="image/*">
                        <small class="text-muted">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection