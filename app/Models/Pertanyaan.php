<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'tm_pertanyaan';

    protected $fillable = [
        'indikator_id',
        'kode',
        'pertanyaan',
        'penjelasan',
        'tipe_jawaban',
        'pilihan_jawaban',
        'urutan',
        'status',
    ];

    protected $casts = [
        'pilihan_jawaban' => 'array',
        'status' => 'integer',
    ];

    /**
     * Relationship: Pertanyaan belongs to Indikator
     */
    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    /**
     * Relationship: Pertanyaan has many SubPertanyaan
     */
    public function subPertanyaans()
    {
        return $this->hasMany(SubPertanyaan::class)->orderBy('urutan');
    }

    /**
     * Accessor: Check apakah pertanyaan memiliki sub-pertanyaan
     */
    public function getHasSubPertanyaanAttribute()
    {
        return $this->subPertanyaans()->count() > 0;
    }

    /**
     * Accessor: Parse penjelasan pilihan ganda menjadi array terstruktur
     * Handle format: "a. Text b. Text c. Text" atau "a. Text\nb. Text"
     *
     * @return array
     */
    public function getPenjelasanListAttribute()
    {
        // Return empty array jika bukan pilihan ganda atau penjelasan kosong
        if (!$this->penjelasan || $this->tipe_jawaban !== 'pilihan_ganda') {
            return [];
        }

        $result = [];
        $text = $this->penjelasan;

        // Regex untuk match pattern: "a. Text..." sampai sebelum "b. Text..." dst
        // Pattern ini akan handle baik spasi maupun newline sebagai separator
        preg_match_all('/([a-z])\.\s*(.+?)(?=\s+[a-z]\.|$)/is', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $result[] = [
                'opsi' => strtoupper($match[1]),  // A, B, C, dst
                'text' => trim($match[2])          // Text penjelasan
            ];
        }

        return $result;
    }
}
