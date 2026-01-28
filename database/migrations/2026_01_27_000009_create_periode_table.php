<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tm_periode', function (Blueprint $table) {
            $table->id();
            $table->year('tahun'); // 2024, 2025, 2026
            $table->string('nama_periode', 100); // Zona Integritas 2024, WBK/WBBM 2025
            $table->date('tanggal_mulai'); // Kapan mulai pengisian
            $table->date('tanggal_selesai'); // Deadline pengisian
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai', 'ditutup'])->default('draft');
            // draft: belum dibuka, aktif: sedang berjalan, selesai: sudah deadline, ditutup: sudah finalisasi
            $table->tinyInteger('is_template')->default(0)->comment('1: template untuk copy, 0: periode normal');
            $table->foreignId('copied_from_periode_id')->nullable()->constrained('tm_periode')->nullOnDelete()
                ->comment('ID periode sumber jika di-copy dari periode lain');
            $table->timestamps();

            $table->unique(['tahun', 'nama_periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_periode');
    }
};
