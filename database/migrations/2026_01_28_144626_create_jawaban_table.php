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
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('tm_periode')->onDelete('cascade');
            $table->foreignId('opd_id')->constrained('tm_opd')->onDelete('cascade');
            $table->foreignId('pertanyaan_id')->constrained('tm_pertanyaan')->onDelete('cascade');
            $table->foreignId('sub_pertanyaan_id')->nullable()->constrained('tm_sub_pertanyaan')->onDelete('cascade');

            // Jawaban fields
            $table->text('jawaban_text')->nullable(); // untuk ya_tidak, pilihan_ganda
            $table->decimal('jawaban_angka', 15, 2)->nullable(); // untuk angka, desimal
            $table->text('keterangan')->nullable(); // catatan tambahan

            // Meta data
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Unique constraint: satu pertanyaan hanya bisa dijawab sekali per periode per OPD
            $table->unique(['periode_id', 'opd_id', 'pertanyaan_id', 'sub_pertanyaan_id'], 'unique_jawaban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};
