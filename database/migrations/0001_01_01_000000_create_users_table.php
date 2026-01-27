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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->nullable()->constrained('tm_opd')->nullOnDelete();
            $table->string('nama_instansi');
            $table->string('nama_kepala')->nullable();
            $table->string('jabatan_kepala')->nullable();
            $table->string('nama_operator')->nullable();
            $table->string('jabatan_operator')->nullable();
            $table->string('email')->unique();
            $table->string('telp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'operator', 'verifikator'])->default('operator');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
