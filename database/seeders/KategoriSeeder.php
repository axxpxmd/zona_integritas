<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            // Kategori untuk Komponen A. PENGUNGKIT
            [
                'komponen_id' => 1, // A. PENGUNGKIT
                'kode' => 'I',
                'nama' => 'PEMENUHAN',
                'bobot' => 30.00,
                'deskripsi' => 'Kategori Pemenuhan mengukur aspek-aspek dasar yang harus dipenuhi dalam pembangunan Zona Integritas',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'komponen_id' => 1, // A. PENGUNGKIT
                'kode' => 'II',
                'nama' => 'REFORM',
                'bobot' => 30.00,
                'deskripsi' => 'Kategori Reform mengukur upaya reformasi dan perbaikan yang dilakukan unit kerja',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kategori untuk Komponen B. HASIL
            [
                'komponen_id' => 2, // B. HASIL
                'kode' => 'I',
                'nama' => 'BIROKRASI YANG BERSIH DAN AKUNTABEL',
                'bobot' => 22.50,
                'deskripsi' => 'Kategori yang mengukur pencapaian dalam mewujudkan birokrasi yang bersih dan akuntabel',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'komponen_id' => 2, // B. HASIL
                'kode' => 'II',
                'nama' => 'PELAYANAN PUBLIK YANG PRIMA',
                'bobot' => 17.50,
                'deskripsi' => 'Kategori yang mengukur kualitas pelayanan publik yang diberikan kepada masyarakat',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tm_kategori')->insert($kategoris);
    }
}
