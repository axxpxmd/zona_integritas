<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kuesioner_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('tm_periode')->cascadeOnDelete(); // Periode pengisian
            $table->foreignId('opd_id')->constrained('tm_opd')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('tm_pertanyaan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Siapa yang mengisi
            $table->text('jawaban'); // Jawaban bisa A/B/C/Ya/Tidak/Angka/Teks
            $table->decimal('nilai', 5, 2)->nullable(); // Nilai dari jawaban (jika ada scoring)
            $table->text('catatan')->nullable(); // Catatan tambahan dari pengisi
            $table->text('bukti_dokumen')->nullable(); // Path file upload jika ada
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_verifikator')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['periode_id', 'opd_id', 'pertanyaan_id'], 'jawaban_unique');
            $table->index(['periode_id', 'status_verifikasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuesioner_jawaban');
    }
};
