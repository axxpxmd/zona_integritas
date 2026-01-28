<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubPertanyaan extends Model
{
    protected $table = 'tm_sub_pertanyaan';

    protected $fillable = [
        'pertanyaan_id',
        'kode',
        'pertanyaan',
        'penjelasan',
        'tipe_input',
        'satuan',
        'formula',
        'urutan',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Relationship: SubPertanyaan belongs to Pertanyaan
     * Menggunakan nama pertanyaanUtama untuk menghindari conflict dengan field 'pertanyaan'
     */
    public function pertanyaanUtama()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }
}
