<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'id',
        'periode_id',
        'opd_id',
        'pertanyaan_id',
        'sub_pertanyaan_id',
        'jawaban_text',
        'jawaban_angka',
        'nilai',
        'keterangan',
        'file_path',
        'status_verifikasi',
        'verifikator_jawaban_text',
        'verifikator_jawaban_angka',
        'catatan_verifikator',
        'revisi_count',
        'revised_at',
        'revised_by',
        'menunggu_dicek_ulang',
        'verified_by',
        'verified_at',
        'status_verifikasi_menpan',
        'menpan_jawaban_text',
        'menpan_jawaban_angka',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'catatan_revisi',
        'menpan_verified_by',
        'menpan_verified_at',
    ];

    protected $casts = [
        'jawaban_angka' => 'float',
        'nilai' => 'float',
        'menpan_jawaban_angka' => 'float',
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
