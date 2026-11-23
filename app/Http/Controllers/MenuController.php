<?php

namespace App\Http\Controllers;

use App\Models\Kantin;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $kantinSaya = $user->isPenjual() ? $user->kantin : null;

        $menus = Menu::with(['kantin', 'kategori'])
            // Penjual hanya melihat menu kantinnya sendiri
            ->when($user->isPenjual(), function ($query) use ($kantinSaya) {
                $query->where('kantin_id', $kantinSaya?->id ?? 0);
            })
            ->when($request->cari, function ($query, $cari) {
                $query->where('nama_menu', 'like', "%{$cari}%");
            })
            ->when($request->kategori_id, function ($query, $kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->orderBy('nama_menu')
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $kantins = $user->isAdmin()
            ? Kantin::orderBy('nama_kantin')->get()
            : collect();

        return view('menu.index', compact('menus', 'kategoris', 'kantins', 'kantinSaya'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['kantin_id'] = $this->tentukanKantin($request);
        $data['tersedia'] = $request->boolean('tersedia');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        Menu::create($data);

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $this->pastikanBolehAkses($request, $menu);

        $data = $this->validasi($request);
        $data['tersedia'] = $request->boolean('tersedia');

        if ($request->user()->isAdmin()) {
            $data['kantin_id'] = $this->tentukanKantin($request);
        }

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Request $request, Menu $menu)
    {
        $this->pastikanBolehAkses($request, $menu);

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'nama_menu' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0', 'max:10000000'],
            'stok' => ['required', 'integer', 'min:0', 'max:100000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    // Penjual otomatis memakai kantinnya sendiri, admin memilih kantin lewat formulir
    private function tentukanKantin(Request $request): int
    {
        $user = $request->user();

        if ($user->isPenjual()) {
            abort_unless($user->kantin, 403, 'Anda belum memiliki kantin. Hubungi admin.');
            return $user->kantin->id;
        }

        $request->validate(['kantin_id' => ['required', 'exists:kantins,id']]);

        return (int) $request->kantin_id;
    }

    // Penjual tidak boleh mengubah atau menghapus menu milik kantin lain
    private function pastikanBolehAkses(Request $request, Menu $menu): void
    {
        $user = $request->user();

        if ($user->isPenjual()) {
            abort_unless(
                $user->kantin && $menu->kantin_id === $user->kantin->id,
                403,
                'Anda tidak memiliki akses ke menu ini.'
            );
        }
    }
}