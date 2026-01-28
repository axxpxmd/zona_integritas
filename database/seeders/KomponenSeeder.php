<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomponenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $komponens = [
            [
                'kode' => 'A',
                'nama' => 'PENGUNGKIT',
                'bobot' => 60.00,
                'deskripsi' => 'Komponen Pengungkit merupakan komponen yang mengukur upaya-upaya yang dilakukan oleh unit kerja dalam mewujudkan Zona Integritas',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'B',
                'nama' => 'HASIL',
                'bobot' => 40.00,
                'deskripsi' => 'Komponen Hasil merupakan komponen yang mengukur hasil atau output dari upaya-upaya yang telah dilakukan dalam Zona Integritas',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tm_komponen')->insert($komponens);
    }
}
