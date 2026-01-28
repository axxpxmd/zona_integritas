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
        Schema::create('tm_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komponen_id')->constrained('tm_komponen')->cascadeOnDelete();
            $table->string('kode', 10); // I, II
            $table->string('nama', 150); // PEMENUHAN, REFORM, BIROKRASI YANG BERSIH DAN AKUNTABEL
            $table->decimal('bobot', 5, 2); // 30, 22.5
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();

            $table->unique(['komponen_id', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_kategori');
    }
};
