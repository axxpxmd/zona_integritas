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
            $table->enum('status_verifikasi_menpan', ['belum_diverifikasi', 'disetujui'])
                ->default('belum_diverifikasi')
                ->after('verified_at');
            $table->text('menpan_jawaban_text')->nullable()->after('status_verifikasi_menpan');
            $table->decimal('menpan_jawaban_angka', 15, 2)->nullable()->after('menpan_jawaban_text');
            $table->foreignId('menpan_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('menpan_jawaban_angka');
            $table->timestamp('menpan_verified_at')->nullable()->after('menpan_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menpan_verified_by');
            $table->dropColumn([
                'status_verifikasi_menpan',
                'menpan_jawaban_text',
                'menpan_jawaban_angka',
                'menpan_verified_at',
            ]);
        });
    }
};
