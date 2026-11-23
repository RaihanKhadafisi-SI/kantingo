@extends('layouts.admin')

@section('title', 'Data Menu')
@section('judul', 'Data Menu')

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

    @if (auth()->user()->isPenjual() && ! $kantinSaya)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Akun Anda belum terhubung dengan kantin. Silakan hubungi admin untuk dibuatkan kantin terlebih dahulu.
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                Daftar Menu
                @if ($kantinSaya)
                    <small class="text-muted">- {{ $kantinSaya->nama_kantin }}</small>
                @endif
            </h3>
            <div class="card-tools">
                @if (auth()->user()->isAdmin() || $kantinSaya)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus"></i> Tambah Menu
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('menu.index') }}" class="form-inline mb-3">
                <input type="text" name="cari" value="{{ request('cari') }}"
                       class="form-control mr-2" placeholder="Cari nama menu...">
                <select name="kategori_id" class="form-control mr-2">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-info mr-1"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('menu.index') }}" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">No</th>
                            <th style="width: 90px">Foto</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            @if (auth()->user()->isAdmin())
                                <th>Kantin</th>
                            @endif
                            <th>Harga</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status</th>
                            <th style="width: 130px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $loop->index }}</td>
                                <td class="text-center">
                                    @if ($menu->foto)
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="Foto"
                                             style="width:70px;height:55px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <span class="text-muted"><i class="fas fa-hamburger fa-2x"></i></span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $menu->nama_menu }}</strong>
                                    @if ($menu->deskripsi)
                                        <br><small class="text-muted">{{ $menu->deskripsi }}</small>
                                    @endif
                                </td>
                                <td>{{ $menu->kategori->nama_kategori ?? '-' }}</td>
                                @if (auth()->user()->isAdmin())
                                    <td>{{ $menu->kantin->nama_kantin ?? '-' }}</td>
                                @endif
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($menu->stok <= 0)
                                        <span class="badge badge-danger">Habis</span>
                                    @elseif ($menu->stok <= 5)
                                        <span class="badge badge-warning">{{ $menu->stok }}</span>
                                    @else
                                        <span class="badge badge-info">{{ $menu->stok }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($menu->tersedia)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm"
                                            data-toggle="modal" data-target="#modalEdit{{ $menu->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('menu.destroy', $menu) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus menu {{ $menu->nama_menu }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="modalEdit{{ $menu->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('menu.update', $menu) }}" method="POST"
                                          enctype="multipart/form-data" class="modal-content">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Menu</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Nama Menu</label>
                                                    <input type="text" name="nama_menu" class="form-control"
                                                           value="{{ $menu->nama_menu }}" required>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Kategori</label>
                                                    <select name="kategori_id" class="form-control" required>
                                                        @foreach ($kategoris as $kategori)
                                                            <option value="{{ $kategori->id }}"
                                                                {{ $menu->kategori_id == $kategori->id ? 'selected' : '' }}>
                                                                {{ $kategori->nama_kategori }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            @if (auth()->user()->isAdmin())
                                                <div class="form-group">
                                                    <label>Kantin</label>
                                                    <select name="kantin_id" class="form-control" required>
                                                        @foreach ($kantins as $kantin)
                                                            <option value="{{ $kantin->id }}"
                                                                {{ $menu->kantin_id == $kantin->id ? 'selected' : '' }}>
                                                                {{ $kantin->nama_kantin }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label>Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $menu->deskripsi }}</textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>Harga (Rp)</label>
                                                    <input type="number" name="harga" class="form-control"
                                                           value="{{ $menu->harga }}" min="0" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Stok</label>
                                                    <input type="number" name="stok" class="form-control"
                                                           value="{{ $menu->stok }}" min="0" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Status</label>
                                                    <div class="custom-control custom-switch mt-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="tersedia{{ $menu->id }}" name="tersedia" value="1"
                                                               {{ $menu->tersedia ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="tersedia{{ $menu->id }}">Tersedia</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Foto Menu (kosongkan jika tidak diganti)</label>
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
                                <td colspan="{{ auth()->user()->isAdmin() ? 9 : 8 }}" class="text-center text-muted py-4">
                                    Belum ada data menu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer clearfix">
            {{ $menus->links('pagination::bootstrap-4') }}
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('menu.store') }}" method="POST"
                  enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Menu</label>
                            <input type="text" name="nama_menu" class="form-control"
                                   placeholder="Contoh: Nasi Goreng Spesial" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kategori</label>
                            <select name="kategori_id" class="form-control" required>
                                <option value="">-- Pilih kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <div class="form-group">
                            <label>Kantin</label>
                            <select name="kantin_id" class="form-control" required>
                                <option value="">-- Pilih kantin --</option>
                                @foreach ($kantins as $kantin)
                                    <option value="{{ $kantin->id }}">{{ $kantin->nama_kantin }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"
                                  placeholder="Keterangan singkat (boleh kosong)"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" min="0" placeholder="15000" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" min="0" value="20" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Status</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input"
                                       id="tersediaBaru" name="tersedia" value="1" checked>
                                <label class="custom-control-label" for="tersediaBaru">Tersedia</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Menu</label>
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