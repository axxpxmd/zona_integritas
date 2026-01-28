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
        Schema::create('kuesioner_sub_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jawaban_id')->constrained('kuesioner_jawaban')->cascadeOnDelete();
            $table->foreignId('sub_pertanyaan_id')->constrained('tm_sub_pertanyaan')->cascadeOnDelete();
            $table->text('nilai_input'); // Input dari user (jumlah/angka/teks)
            $table->decimal('nilai_hasil', 10, 2)->nullable(); // Hasil perhitungan formula (jika ada)
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['jawaban_id', 'sub_pertanyaan_id'], 'sub_jawaban_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuesioner_sub_jawaban');
    }
};
