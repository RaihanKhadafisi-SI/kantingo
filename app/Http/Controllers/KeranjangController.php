<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index()
    {
        $keranjang = session('keranjang', []);

        $menus = Menu::with('kantin')
            ->whereIn('id', array_keys($keranjang))
            ->get();

        $items = [];
        $total = 0;
        $tersimpan = [];

        foreach ($menus as $menu) {
            $jumlah = $keranjang[$menu->id];
            $subtotal = $menu->harga * $jumlah;
            $total += $subtotal;
            $tersimpan[$menu->id] = $jumlah;

            $items[] = (object) [
                'menu'     => $menu,
                'jumlah'   => $jumlah,
                'subtotal' => $subtotal,
            ];
        }

        // Buang menu yang sudah dihapus dari database
        session(['keranjang' => $tersimpan]);

        return view('keranjang.index', compact('items', 'total'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'jumlah'  => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        if (! $menu->tersedia || $menu->stok <= 0) {
            return back()->with('error', 'Menu ' . $menu->nama_menu . ' sedang tidak tersedia.');
        }

        $keranjang = session('keranjang', []);

        // Satu pesanan hanya boleh berisi menu dari satu kantin
        if (! empty($keranjang)) {
            $kantinLama = Menu::whereIn('id', array_keys($keranjang))->value('kantin_id');

            if ($kantinLama && $kantinLama != $menu->kantin_id) {
                return back()->with('error', 'Keranjang berisi menu dari kantin lain. Selesaikan atau kosongkan keranjang dulu.');
            }
        }

        $jumlahBaru = ($keranjang[$menu->id] ?? 0) + (int) $request->jumlah;

        if ($jumlahBaru > $menu->stok) {
            $jumlahBaru = $menu->stok;
            $keranjang[$menu->id] = $jumlahBaru;
            session(['keranjang' => $keranjang]);

            return back()->with('error', 'Jumlah disesuaikan dengan stok yang ada (' . $menu->stok . ').');
        }

        $keranjang[$menu->id] = $jumlahBaru;
        session(['keranjang' => $keranjang]);

        return back()->with('success', $menu->nama_menu . ' ditambahkan ke keranjang.');
    }

    public function ubah(Request $request, Menu $menu)
    {
        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $keranjang = session('keranjang', []);

        if (! isset($keranjang[$menu->id])) {
            return back();
        }

        $jumlah = min((int) $request->jumlah, $menu->stok);
        $keranjang[$menu->id] = max($jumlah, 1);
        session(['keranjang' => $keranjang]);

        return back()->with('success', 'Jumlah menu diperbarui.');
    }

    public function hapus(Menu $menu)
    {
        $keranjang = session('keranjang', []);
        unset($keranjang[$menu->id]);
        session(['keranjang' => $keranjang]);

        return back()->with('success', 'Menu dihapus dari keranjang.');
    }

    public function kosongkan()
    {
        session()->forget('keranjang');

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}