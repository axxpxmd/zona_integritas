<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'tm_indikator';

    protected $fillable = [
        'sub_kategori_id',
        'kode',
        'nama',
        'bobot',
        'deskripsi',
        'urutan',
        'status',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * Relationship: Indikator belongs to SubKategori
     */
    public function subKategori()
    {
        return $this->belongsTo(SubKategori::class);
    }

    /**
     * Relationship: Indikator has many Pertanyaan
     */
    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class)->orderBy('urutan');
    }
}
