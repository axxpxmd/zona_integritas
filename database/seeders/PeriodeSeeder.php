<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama terlebih dahulu dengan disable foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tm_periode')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $periodes = [
            // Periode tahun 2026 (sedang aktif) - Januari 2026
            [
                'tahun' => 2026,
                'nama_periode' => 'Zona Integritas 2026',
                'tanggal_mulai' => '2026-01-05',
                'tanggal_selesai' => '2026-01-31',
                'deskripsi' => 'Periode penilaian Zona Integritas tahun 2026 - Tahap Pengisian Kuesioner',
                'status' => 'aktif',
                'is_template' => 0,
                'copied_from_periode_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tm_periode')->insert($periodes);
    }
}
