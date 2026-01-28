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
        Schema::create('tm_komponen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // A, B
            $table->string('nama', 100); // PENGUNGKIT, HASIL
            $table->decimal('bobot', 5, 2); // 60, 40
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: aktif, 0: tidak aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_komponen');
    }
};
