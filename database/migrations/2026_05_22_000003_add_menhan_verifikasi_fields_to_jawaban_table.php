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
        Schema::table('jawaban', function (Blueprint $table) {
            $table->enum('status_verifikasi_menhan', ['belum_diverifikasi', 'disetujui'])
                ->default('belum_diverifikasi')
                ->after('verified_at');
            $table->text('menhan_jawaban_text')->nullable()->after('status_verifikasi_menhan');
            $table->decimal('menhan_jawaban_angka', 15, 2)->nullable()->after('menhan_jawaban_text');
            $table->foreignId('menhan_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('menhan_jawaban_angka');
            $table->timestamp('menhan_verified_at')->nullable()->after('menhan_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menhan_verified_by');
            $table->dropColumn([
                'status_verifikasi_menhan',
                'menhan_jawaban_text',
                'menhan_jawaban_angka',
                'menhan_verified_at',
            ]);
        });
    }
};
