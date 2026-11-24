<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanans';

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'kantin_id',
        'total_harga',
        'status',
        'catatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kantin(): BelongsTo
    {
        return $this->belongsTo(Kantin::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public static function buatKode(): string
    {
        do {
            $kode = 'KG-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -4));
        } while (self::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}