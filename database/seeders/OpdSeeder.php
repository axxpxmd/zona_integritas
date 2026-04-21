<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
            ['n_opd' => 'Sekretariat Daerah', 'alamat' => 'Serua, Kec Ciputat'],
            ['n_opd' => 'Sekretariat DPRD', 'alamat' => 'Serua, Kec Ciputat'],
            ['n_opd' => 'Inspektorat', 'alamat' => 'Serua, Kec Ciputat'],
            ['n_opd' => 'Badan Pendapatan Daerah', 'alamat' => 'Serua, Kec Ciputat'],
            ['n_opd' => 'Badan Keuangan dan Aset Daerah', 'alamat' => 'Serua, Kec Ciputat'],
            ['n_opd' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah', 'alamat' => 'Serua, kec.Ciputat'],
            ['n_opd' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia', 'alamat' => 'Cilenggang, Serpong'],
            ['n_opd' => 'Badan Penanggulangan Bencana Daerah', 'alamat' => 'Ciater, Serpong'],
            ['n_opd' => 'Badan Kesatuan Bangsa dan Politik', 'alamat' => 'Mekar Jaya, Serpong'],
            ['n_opd' => 'Dinas Komunikasi dan Informatika', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Sumber Daya Air, Bina Marga dan Bina Konstruksi', 'alamat' => 'Setu'],
            ['n_opd' => 'Dinas Cipta Karya dan Tata Ruang', 'alamat' => 'Lengkong Wetan, Serpong'],
            ['n_opd' => 'Dinas Perumahan, Kawasan Pemukiman dan Pertanahan', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Pendidikan dan Kebudayaan', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Lingkungan Hidup', 'alamat' => 'Setu'],
            ['n_opd' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'alamat' => 'Cilenggang, Serpong'],
            ['n_opd' => 'Dinas Pemadam Kebakaran', 'alamat' => 'Serpong Utara'],
            ['n_opd' => 'Dinas Sosial', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Tenaga Kerja', 'alamat' => 'Setu'],
            ['n_opd' => 'Dinas Pariwisata', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Perhubungan', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Perpustakaan dan Kearsipan Daerah', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Ketahanan Pangan, Pertanian dan Perikanan', 'alamat' => 'Mekar Jaya, Serpong'],
            ['n_opd' => 'Satuan Polisi Pamong Praja', 'alamat' => 'Setu'],
            ['n_opd' => 'Dinas Kependudukan dan Catatan Sipil', 'alamat' => 'Setu'],
            ['n_opd' => 'Dinas Koperasi, Usaha Kecil dan Menengah', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Perindustrian dan Perdagangan', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Kepemudaan dan Olahraga', 'alamat' => 'Serua, Ciputat'],
            ['n_opd' => 'Dinas Kesehatan', 'alamat' => 'Ciater, Serpong'],
            ['n_opd' => 'Kecamatan Setu', 'alamat' => 'Setu'],
            ['n_opd' => 'Kecamatan Serpong', 'alamat' => 'Serpong'],
            ['n_opd' => 'Kecamatan Serpong Utara', 'alamat' => 'Serpong Utara'],
            ['n_opd' => 'Kecamatan Pondok Aren', 'alamat' => 'Pondok Aren'],
            ['n_opd' => 'Kecamatan Ciputat', 'alamat' => 'Ciputat'],
            ['n_opd' => 'Kecamatan Ciputat Timur', 'alamat' => 'Ciputat Timur'],
            ['n_opd' => 'Kecamatan Pamulang', 'alamat' => 'Pamulang'],
            ['n_opd' => 'BPBD Kota Tangerang Selatan', 'alamat' => 'jl. Cendekia No.28 kel. Ciat'],
            ['n_opd' => 'Dinas Pemadam Kebakaran dan Penyelamatan', 'alamat' => 'Jl. Melatimas raya blok J no'],
        ];

        foreach ($opds as $opd) {
            Opd::create([
                'n_opd' => $opd['n_opd'],
                'alamat' => $opd['alamat'],
                'status' => 1,
            ]);
        }
    }
}
