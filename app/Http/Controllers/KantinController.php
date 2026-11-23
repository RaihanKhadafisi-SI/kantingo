<?php

namespace App\Http\Controllers;

use App\Models\Kantin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KantinController extends Controller
{
    public function index(Request $request)
    {
        $kantins = Kantin::with('penjual')
            ->when($request->cari, function ($query, $cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('nama_kantin', 'like', "%{$cari}%")
                      ->orWhere('lokasi', 'like', "%{$cari}%");
                });
            })
            ->orderBy('nama_kantin')
            ->paginate(10)
            ->withQueryString();

        $penjuals = User::where('role', 'penjual')->orderBy('name')->get();

        return view('kantin.index', compact('kantins', 'penjuals'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kantin', 'public');
        }

        Kantin::create($data);

        return redirect()->route('kantin.index')
            ->with('success', 'Kantin berhasil ditambahkan.');
    }

    public function update(Request $request, Kantin $kantin)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            if ($kantin->foto) {
                Storage::disk('public')->delete($kantin->foto);
            }
            $data['foto'] = $request->file('foto')->store('kantin', 'public');
        }

        $kantin->update($data);

        return redirect()->route('kantin.index')
            ->with('success', 'Kantin berhasil diperbarui.');
    }

    public function destroy(Kantin $kantin)
    {
        if ($kantin->foto) {
            Storage::disk('public')->delete($kantin->foto);
        }

        $kantin->delete();

        return redirect()->route('kantin.index')
            ->with('success', 'Kantin berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'nama_kantin' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:buka,tutup'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}