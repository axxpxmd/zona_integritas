<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubPertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama terlebih dahulu dengan disable foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tm_sub_pertanyaan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $subPertanyaans = [
            // Pertanyaan ID 88 (Indikator 26a): Agen perubahan telah membuat perubahan yang konkret
            [
                'pertanyaan_id' => 88,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah Agen Perubahan',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 88,
                'kode' => 'b',
                'pertanyaan' => 'Jumlah Perubahan yang dibuat',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pertanyaan ID 89 (Indikator 26b): Perubahan yang dibuat Agen Perubahan telah terintegrasi
            [
                'pertanyaan_id' => 89,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah Perubahan yang dibuat',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 89,
                'kode' => 'b',
                'pertanyaan' => 'Jumlah Perubahan yang telah diintegrasikan dalam sistem manajemen',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pertanyaan ID 100 (Indikator 34a): Penurunan pelanggaran disiplin pegawai
            [
                'pertanyaan_id' => 100,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah pelanggaran tahun sebelumnya',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 100,
                'kode' => 'b',
                'pertanyaan' => 'Jumlah pelanggaran tahun ini',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 100,
                'kode' => 'c',
                'pertanyaan' => 'Jumlah pelanggaran yang telah diberikan sanksi/hukuman',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pertanyaan ID 101 (Indikator 35a): Persentase Sasaran dengan capaian 100% atau lebih
            [
                'pertanyaan_id' => 101,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah Sasaran Kinerja',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 101,
                'kode' => 'b',
                'pertanyaan' => 'Jumlah Sasaran Kinerja yang tercapai 100% atau lebih',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pertanyaan ID 105 (Indikator 39a): Persentase penanganan pengaduan masyarakat
            [
                'pertanyaan_id' => 105,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah pengaduan masyarakat yang harus ditindaklanjuti',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 105,
                'kode' => 'b',
                'pertanyaan' => 'Jumlah pengaduan masyarakat yang sedang diproses',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 105,
                'kode' => 'c',
                'pertanyaan' => 'Jumlah pengaduan masyarakat yang selesai ditindaklanjuti',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pertanyaan ID 106 (Indikator 40a): Persentase penyampaian LHKPN
            [
                'pertanyaan_id' => 106,
                'kode' => 'a',
                'pertanyaan' => 'Jumlah yang harus melaporkan',
                'penjelasan' => 'Perhitungan: Kepala satuan kerja + Pejabat yang diwajibkan menyampaikan LHKPN + Lainnya',
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => '=K179+K180+K181',
                'urutan' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 106,
                'kode' => 'b',
                'pertanyaan' => 'Kepala satuan kerja',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 106,
                'kode' => 'c',
                'pertanyaan' => 'Pejabat yang diwajibkan menyampaikan LHKPN',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 106,
                'kode' => 'd',
                'pertanyaan' => 'Lainnya',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 4,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertanyaan_id' => 106,
                'kode' => 'e',
                'pertanyaan' => 'Jumlah yang sudah melaporkan',
                'penjelasan' => null,
                'tipe_input' => 'jumlah',
                'satuan' => 'Jumlah',
                'formula' => null,
                'urutan' => 5,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tm_sub_pertanyaan')->insert($subPertanyaans);
    }
}
