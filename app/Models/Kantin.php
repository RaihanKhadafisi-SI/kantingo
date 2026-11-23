<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kantin extends Model
{
    use HasFactory;

    protected $table = 'kantins';

    protected $fillable = [
        'user_id',
        'nama_kantin',
        'lokasi',
        'deskripsi',
        'jam_buka',
        'jam_tutup',
        'status',
        'foto',
    ];

    public function penjual(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}