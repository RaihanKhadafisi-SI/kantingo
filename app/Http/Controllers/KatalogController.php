<?php

namespace App\Http\Controllers;

use App\Models\Kantin;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::with(['kantin', 'kategori'])
            ->where('tersedia', true)
            ->where('stok', '>', 0)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_menu', 'like', '%' . $request->cari . '%');
            })
            ->when($request->filled('kategori_id'), function ($query) use ($request) {
                $query->where('kategori_id', $request->kategori_id);
            })
            ->when($request->filled('kantin_id'), function ($query) use ($request) {
                $query->where('kantin_id', $request->kantin_id);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $kategoris = Kategori::all();
        $kantins = Kantin::all();

        return view('katalog.index', compact('menus', 'kategoris', 'kantins'));
    }
}