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
        Schema::create('tm_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kategori_id')->constrained('tm_sub_kategori')->cascadeOnDelete();
            $table->string('kode', 10); // i, ii, iii, iv, v, vi
            $table->string('nama', 250); // Tim Reformasi Birokrasi, Rencana Pembangunan Zona Integritas, dst
            $table->decimal('bobot', 5, 2); // 0.5, 1, 1.5, 2, 2.5
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();

            $table->unique(['sub_kategori_id', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_indikator');
    }
};
