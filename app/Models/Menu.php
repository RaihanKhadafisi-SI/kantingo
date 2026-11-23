<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'kantin_id',
        'kategori_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'stok',
        'foto',
        'tersedia',
    ];

    protected function casts(): array
    {
        return [
            'tersedia' => 'boolean',
            'harga' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function kantin(): BelongsTo
    {
        return $this->belongsTo(Kantin::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}