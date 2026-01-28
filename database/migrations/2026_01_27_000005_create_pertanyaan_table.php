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
        Schema::create('tm_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('tm_indikator')->cascadeOnDelete();
            $table->string('kode', 10); // a, b, c, d, e, f, g, h
            $table->text('pertanyaan'); // Teks pertanyaan
            $table->text('penjelasan')->nullable(); // Penjelasan jawaban
            $table->enum('tipe_jawaban', ['ya_tidak', 'pilihan_ganda', 'angka', 'teks'])->default('pilihan_ganda');
            $table->json('pilihan_jawaban')->nullable(); // JSON untuk pilihan A/B/C/D/E atau Ya/Tidak
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();

            $table->unique(['indikator_id', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_pertanyaan');
    }
};
