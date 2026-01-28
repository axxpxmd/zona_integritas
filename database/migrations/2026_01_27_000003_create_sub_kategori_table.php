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
        Schema::create('tm_sub_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('tm_kategori')->cascadeOnDelete();
            $table->string('kode', 10); // 1, 2, 3, 4, 5, 6 atau a, b
            $table->string('nama', 200); // MANAJEMEN PERUBAHAN, PENATAAN TATALAKSANA, dst
            $table->decimal('bobot', 5, 2); // 4, 3.5, 5, 7.5, 17.5
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();

            $table->unique(['kategori_id', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_sub_kategori');
    }
};
