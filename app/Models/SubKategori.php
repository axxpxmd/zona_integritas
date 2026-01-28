<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategori extends Model
{
    protected $table = 'tm_sub_kategori';

    protected $fillable = [
        'kategori_id',
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
     * Relationship: SubKategori belongs to Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relationship: SubKategori has many Indikator
     */
    public function indikators()
    {
        return $this->hasMany(Indikator::class)->orderBy('urutan');
    }
}
