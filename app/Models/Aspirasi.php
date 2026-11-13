<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aspirasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tiket',
        'user_id',
        'category_id',
        'nama',
        'kontak',
        'kelas_instansi',
        'is_anonim',
        'judul',
        'pesan',
        'lampiran',
        'status',
        'is_public',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class)->latest();
    }

    public function latestTanggapan(): HasOne
    {
        return $this->hasOne(Tanggapan::class)->latestOfMany();
    }

    // Helper accessor untuk nama tampilan (Anonim vs Nama asli)
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonim) {
            return 'Anonim';
        }
        return $this->nama ?: 'Warga / Siswa';
    }
}
