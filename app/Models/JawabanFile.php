<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanFile extends Model
{
    protected $table = 'jawaban_files';

    protected $fillable = [
        'jawaban_id',
        'revisi_ke',
        'original_name',
        'file_path',
        'size',
        'mime_type',
        'uploaded_by',
    ];

    public function jawaban()
    {
        return $this->belongsTo(Jawaban::class);
    }
}
