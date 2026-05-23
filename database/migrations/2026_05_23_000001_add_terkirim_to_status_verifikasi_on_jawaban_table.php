<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE jawaban MODIFY status_verifikasi ENUM('belum_diverifikasi','disetujui','direvisi','terkirim') NOT NULL DEFAULT 'belum_diverifikasi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('jawaban')
            ->where('status_verifikasi', 'terkirim')
            ->update(['status_verifikasi' => 'disetujui']);

        DB::statement("ALTER TABLE jawaban MODIFY status_verifikasi ENUM('belum_diverifikasi','disetujui','direvisi') NOT NULL DEFAULT 'belum_diverifikasi'");
    }
};
