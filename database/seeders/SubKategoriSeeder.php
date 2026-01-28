<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subKategoris = [
            // Sub Kategori untuk Kategori I. PEMENUHAN (komponen A)
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '1',
                'nama' => 'MANAJEMEN PERUBAHAN',
                'bobot' => 4.00,
                'deskripsi' => 'Sub kategori yang mengukur manajemen perubahan dalam unit kerja',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '2',
                'nama' => 'PENATAAN TATALAKSANA',
                'bobot' => 3.50,
                'deskripsi' => 'Sub kategori yang mengukur penataan tatalaksana organisasi',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '3',
                'nama' => 'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur sistem manajemen SDM aparatur',
                'urutan' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '4',
                'nama' => 'PENGUATAN AKUNTABILITAS',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur penguatan akuntabilitas kinerja',
                'urutan' => 4,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '5',
                'nama' => 'PENGUATAN PENGAWASAN',
                'bobot' => 7.50,
                'deskripsi' => 'Sub kategori yang mengukur penguatan fungsi pengawasan',
                'urutan' => 5,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1, // I. PEMENUHAN
                'kode' => '6',
                'nama' => 'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur peningkatan kualitas pelayanan publik',
                'urutan' => 6,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub Kategori untuk Kategori II. REFORM (komponen A)
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '1',
                'nama' => 'MANAJEMEN PERUBAHAN',
                'bobot' => 4.00,
                'deskripsi' => 'Sub kategori yang mengukur manajemen perubahan dalam reformasi birokrasi',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '2',
                'nama' => 'PENATAAN TATALAKSANA',
                'bobot' => 3.50,
                'deskripsi' => 'Sub kategori yang mengukur penataan tatalaksana dalam reformasi',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '3',
                'nama' => 'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur penataan sistem SDM dalam reformasi',
                'urutan' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '4',
                'nama' => 'PENGUATAN AKUNTABILITAS',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur penguatan akuntabilitas dalam reformasi',
                'urutan' => 4,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '5',
                'nama' => 'PENGUATAN PENGAWASAN',
                'bobot' => 7.50,
                'deskripsi' => 'Sub kategori yang mengukur penguatan pengawasan dalam reformasi',
                'urutan' => 5,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2, // II. REFORM
                'kode' => '6',
                'nama' => 'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
                'bobot' => 5.00,
                'deskripsi' => 'Sub kategori yang mengukur peningkatan kualitas pelayanan dalam reformasi',
                'urutan' => 6,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub Kategori untuk Kategori I. BIROKRASI YANG BERSIH DAN AKUNTABEL (komponen B)
            [
                'kategori_id' => 3, // I. BIROKRASI YANG BERSIH DAN AKUNTABEL
                'kode' => 'a',
                'nama' => 'Nilai Survey Persepsi Korupsi (Survei Eksternal)',
                'bobot' => 17.50,
                'deskripsi' => 'Mengukur persepsi anti korupsi berdasarkan survei eksternal',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3, // I. BIROKRASI YANG BERSIH DAN AKUNTABEL
                'kode' => 'b',
                'nama' => 'Capaian Kinerja Lebih Baik dari pada Capaian Kinerja Sebelumnya',
                'bobot' => 5.00,
                'deskripsi' => 'Mengukur peningkatan capaian kinerja dibandingkan periode sebelumnya',
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sub Kategori untuk Kategori II. PELAYANAN PUBLIK YANG PRIMA (komponen B)
            [
                'kategori_id' => 4, // II. PELAYANAN PUBLIK YANG PRIMA
                'kode' => 'a',
                'nama' => 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)',
                'bobot' => 17.50,
                'deskripsi' => 'Mengukur kualitas pelayanan berdasarkan survei eksternal',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tm_sub_kategori')->insert($subKategoris);
    }
}
