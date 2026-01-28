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
        Schema::create('tm_sub_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertanyaan_id')->constrained('tm_pertanyaan')->cascadeOnDelete();
            $table->string('kode', 20); // -, a, b, c (untuk sub dari pertanyaan)
            $table->text('pertanyaan'); // Teks sub-pertanyaan
            $table->text('penjelasan')->nullable();
            $table->enum('tipe_input', ['jumlah', 'persen', 'teks', 'angka'])->default('jumlah');
            $table->string('satuan', 50)->nullable(); // Jumlah, %, Rp, dll
            $table->text('formula')->nullable(); // Formula perhitungan jika ada (untuk kolom K di Excel)
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();

            $table->unique(['pertanyaan_id', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_sub_pertanyaan');
    }
};
