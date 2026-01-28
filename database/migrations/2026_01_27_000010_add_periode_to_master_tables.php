<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan periode_id ke tabel master untuk versioning data per periode
     */
    public function up(): void
    {
        // Tambahkan periode_id ke tabel komponen
        Schema::table('tm_komponen', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
            $table->dropUnique(['kode']); // Hapus unique kode lama
            $table->unique(['periode_id', 'kode']); // Unique per periode
        });

        // Tambahkan periode_id ke tabel kategori
        Schema::table('tm_kategori', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
        });

        // Tambahkan periode_id ke tabel sub_kategori
        Schema::table('tm_sub_kategori', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
        });

        // Tambahkan periode_id ke tabel indikator
        Schema::table('tm_indikator', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
        });

        // Tambahkan periode_id ke tabel pertanyaan
        Schema::table('tm_pertanyaan', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
        });

        // Tambahkan periode_id ke tabel sub_pertanyaan
        Schema::table('tm_sub_pertanyaan', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('id')
                ->constrained('tm_periode')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tm_sub_pertanyaan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::table('tm_pertanyaan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::table('tm_indikator', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::table('tm_sub_kategori', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::table('tm_kategori', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::table('tm_komponen', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
            $table->dropUnique(['periode_id', 'kode']);
            $table->unique('kode');
        });
    }
};
