<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    protected $table = 'tm_komponen';

    protected $fillable = [
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
     * Relationship: Komponen has many Kategori
     */
    public function kategoris()
    {
        return $this->hasMany(Kategori::class)->orderBy('urutan');
    }
}
