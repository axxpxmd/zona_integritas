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
        'nilai',
        'keterangan',
        'file_path',
        'status_verifikasi_menhan',
        'menhan_jawaban_text',
        'menhan_jawaban_angka',
        'menhan_verified_by',
        'menhan_verified_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jawaban_angka' => 'float',
        'nilai' => 'float',
        'menhan_jawaban_angka' => 'float',
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

    public function files()
    {
        return $this->hasMany(JawabanFile::class, 'jawaban_id');
    }
}
