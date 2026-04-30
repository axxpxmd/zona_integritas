<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Menambahkan kolom untuk tracking alur revisi jawaban:
     * - revisi_count       : berapa kali pertanyaan ini sudah direvisi
     * - revised_by         : siapa operator yang terakhir merevisi
     * - revised_at         : kapan operator mengirim revisi terakhir
     * - menunggu_dicek_ulang : flag bahwa operator sudah merevisi dan verifikator perlu mengecek ulang
     */
    public function up(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->unsignedSmallInteger('revisi_count')->default(0)->after('catatan_verifikator')->comment('Berapa kali jawaban ini sudah direvisi oleh operator');
            $table->timestamp('revised_at')->nullable()->after('revisi_count')->comment('Kapan terakhir operator mengirim revisi');
            $table->foreignId('revised_by')->nullable()->after('revised_at')->constrained('users')->onDelete('set null')->comment('User (operator) yang terakhir merevisi jawaban');
            $table->boolean('menunggu_dicek_ulang')->default(false)->after('revised_by')->comment('True jika operator sudah merevisi dan menunggu verifikator mengecek ulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->dropForeign(['revised_by']);
            $table->dropColumn(['revisi_count', 'revised_at', 'revised_by', 'menunggu_dicek_ulang']);
        });
    }
};
