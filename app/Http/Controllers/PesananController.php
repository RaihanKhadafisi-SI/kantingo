<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    // Riwayat pesanan milik pembeli yang sedang login
    public function index()
    {
        $pesanans = Pesanan::with('kantin')
            ->withCount('details')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pesanan.index', compact('pesanans'));
    }

    // Halaman konfirmasi sebelum pesanan dibuat
    public function buat()
    {
        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang masih kosong.');
        }

        $menus = Menu::with('kantin')->whereIn('id', array_keys($keranjang))->get();

        if ($menus->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'Menu di keranjang sudah tidak tersedia.');
        }

        $items = [];
        $total = 0;

        foreach ($menus as $menu) {
            $jumlah = $keranjang[$menu->id];
            $subtotal = $menu->harga * $jumlah;
            $total += $subtotal;

            $items[] = (object) [
                'menu'     => $menu,
                'jumlah'   => $jumlah,
                'subtotal' => $subtotal,
            ];
        }

        $kantin = $menus->first()->kantin;

        return view('pesanan.buat', compact('items', 'total', 'kantin'));
    }

    // Simpan pesanan ke database
    public function simpan(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang masih kosong.');
        }

        try {
            $pesanan = DB::transaction(function () use ($request, $keranjang) {
                $menus = Menu::whereIn('id', array_keys($keranjang))
                    ->lockForUpdate()
                    ->get();

                if ($menus->isEmpty()) {
                    throw new \RuntimeException('Menu di keranjang sudah tidak tersedia.');
                }

                if ($menus->pluck('kantin_id')->unique()->count() > 1) {
                    throw new \RuntimeException('Satu pesanan hanya boleh berisi menu dari satu kantin.');
                }

                $total = 0;

                foreach ($menus as $menu) {
                    $jumlah = $keranjang[$menu->id];

                    if (! $menu->tersedia || $menu->stok < $jumlah) {
                        throw new \RuntimeException('Stok ' . $menu->nama_menu . ' tidak mencukupi.');
                    }

                    $total += $menu->harga * $jumlah;
                }

                $pesanan = Pesanan::create([
                    'kode_pesanan' => Pesanan::buatKode(),
                    'user_id'      => auth()->id(),
                    'kantin_id'    => $menus->first()->kantin_id,
                    'total_harga'  => $total,
                    'status'       => 'menunggu',
                    'catatan'      => $request->catatan,
                ]);

                foreach ($menus as $menu) {
                    $jumlah = $keranjang[$menu->id];

                    DetailPesanan::create([
                        'pesanan_id'   => $pesanan->id,
                        'menu_id'      => $menu->id,
                        'nama_menu'    => $menu->nama_menu,
                        'harga_satuan' => $menu->harga,
                        'jumlah'       => $jumlah,
                        'subtotal'     => $menu->harga * $jumlah,
                    ]);

                    $menu->decrement('stok', $jumlah);
                }

                return $pesanan;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('keranjang.index')->with('error', $e->getMessage());
        }

        session()->forget('keranjang');

        return redirect()->route('pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibuat. Kode pesanan: ' . $pesanan->kode_pesanan);
    }

    // Detail satu pesanan
    public function show(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        $pesanan->load(['kantin', 'details']);

        return view('pesanan.show', compact('pesanan'));
    }

    // Pembeli membatalkan pesanan (hanya saat masih menunggu)
    public function batalkan(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        if ($pesanan->status !== 'menunggu') {
            return back()->with('error', 'Pesanan yang sudah diproses tidak bisa dibatalkan.');
        }

        DB::transaction(function () use ($pesanan) {
            foreach ($pesanan->details as $detail) {
                if ($detail->menu_id) {
                    Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
                }
            }

            $pesanan->update(['status' => 'dibatalkan']);
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}