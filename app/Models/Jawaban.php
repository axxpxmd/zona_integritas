<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'periode_id',
        'opd_id',
        'pertanyaan_id',
        'sub_pertanyaan_id',
        'jawaban_text',
        'jawaban_angka',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jawaban_angka' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }

    public function subPertanyaan()
    {
        return $this->belongsTo(SubPertanyaan::class, 'sub_pertanyaan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
