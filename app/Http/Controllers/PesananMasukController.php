<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananMasukController extends Controller
{
    // Urutan status yang dilalui sebuah pesanan
    private const ALUR = [
        'menunggu' => 'diproses',
        'diproses' => 'siap',
        'siap'     => 'selesai',
    ];

    // Daftar pesanan masuk untuk kantin milik penjual
    public function index(Request $request)
    {
        $kantin = $request->user()->kantin;

        $pesanans = Pesanan::with('user')
            ->withCount('details')
            ->where('kantin_id', $kantin?->id ?? 0)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pesanan_masuk.index', compact('pesanans', 'kantin'));
    }

    // Detail satu pesanan masuk
    public function show(Request $request, Pesanan $pesanan)
    {
        $this->pastikanMilikKantin($request, $pesanan);

        $pesanan->load(['user', 'kantin', 'details']);

        return view('pesanan_masuk.show', compact('pesanan'));
    }

    // Maju ke status berikutnya: menunggu > diproses > siap > selesai
    public function ubahStatus(Request $request, Pesanan $pesanan)
    {
        $this->pastikanMilikKantin($request, $pesanan);

        $berikutnya = self::ALUR[$pesanan->status] ?? null;

        if (! $berikutnya) {
            return back()->with('error', 'Status pesanan ini tidak bisa diubah lagi.');
        }

        $pesanan->update(['status' => $berikutnya]);

        return back()->with('success', 'Status pesanan diubah menjadi ' . ucfirst($berikutnya) . '.');
    }

    // Penjual membatalkan pesanan (hanya saat menunggu atau diproses)
    public function batalkan(Request $request, Pesanan $pesanan)
    {
        $this->pastikanMilikKantin($request, $pesanan);

        if (! in_array($pesanan->status, ['menunggu', 'diproses'])) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan lagi.');
        }

        DB::transaction(function () use ($pesanan) {
            foreach ($pesanan->details as $detail) {
                if ($detail->menu_id) {
                    Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
                }
            }

            $pesanan->update(['status' => 'dibatalkan']);
        });

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    // Penjual tidak boleh membuka pesanan milik kantin lain
    private function pastikanMilikKantin(Request $request, Pesanan $pesanan): void
    {
        $kantin = $request->user()->kantin;

        abort_unless(
            $kantin && (int) $pesanan->kantin_id === (int) $kantin->id,
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );
    }
}