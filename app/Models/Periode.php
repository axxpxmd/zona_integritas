<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'tm_periode';

    protected $fillable = [
        'tahun',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'status',
        'is_template',
        'copied_from_periode_id',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_template' => 'boolean',
    ];

    /**
     * Relasi ke periode sumber (jika di-copy)
     */
    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'copied_from_periode_id');
    }

    /**
     * Relasi ke periode-periode yang di-copy dari periode ini
     */
    public function copiedPeriodes(): HasMany
    {
        return $this->hasMany(Periode::class, 'copied_from_periode_id');
    }

    /**
     * Relasi ke komponen
     */
    public function komponens(): HasMany
    {
        return $this->hasMany(Komponen::class);
    }

    /**
     * Relasi ke jawaban
     */
    public function jawabans(): HasMany
    {
        return $this->hasMany(KuesionerJawaban::class);
    }

    /**
     * Scope untuk periode aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk periode template
     */
    public function scopeTemplate($query)
    {
        return $query->where('is_template', 1);
    }

    /**
     * Scope untuk periode normal (bukan template)
     */
    public function scopeNormal($query)
    {
        return $query->where('is_template', 0);
    }

    /**
     * Check apakah periode sedang berlangsung
     */
    public function isBerlangsung(): bool
    {
        return $this->status === 'aktif'
            && now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'aktif' => 'green',
            'selesai' => 'blue',
            'ditutup' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'ditutup' => 'Ditutup',
            default => $this->status,
        };
    }
}
