<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('admin','operator','verifikator','verifikator_menhan') NOT NULL DEFAULT 'operator'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'verifikator_menhan')
            ->update(['role' => 'verifikator']);

        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('admin','operator','verifikator') NOT NULL DEFAULT 'operator'"
        );
    }
};
