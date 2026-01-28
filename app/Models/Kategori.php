<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'tm_kategori';

    protected $fillable = [
        'komponen_id',
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
     * Relationship: Kategori belongs to Komponen
     */
    public function komponen()
    {
        return $this->belongsTo(Komponen::class);
    }

    /**
     * Relationship: Kategori has many SubKategori
     */
    public function subKategoris()
    {
        return $this->hasMany(SubKategori::class)->orderBy('urutan');
    }
}
