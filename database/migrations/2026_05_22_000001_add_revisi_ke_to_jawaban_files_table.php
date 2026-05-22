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
        Schema::table('jawaban_files', function (Blueprint $table) {
            $table->unsignedInteger('revisi_ke')->nullable()->after('jawaban_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_files', function (Blueprint $table) {
            $table->dropColumn('revisi_ke');
        });
    }
};
