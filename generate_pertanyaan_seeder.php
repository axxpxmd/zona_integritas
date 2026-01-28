<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/public/form kuesioner.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getSheetByName('LKE ZI');

// Mapping indikator_id berdasarkan row Excel
$indikatorMapping = [
    // Sub Kategori 1 - Manajemen Perubahan (Pemenuhan)
    6 => 1,   // i. Penyusunan Tim Kerja
    9 => 2,   // ii. Rencana Pembangunan Zona Integritas
    13 => 3,  // iii. Pemantauan dan Evaluasi
    17 => 4,  // iv. Perubahan pola pikir

    // Sub Kategori 2 - Penataan Tatalaksana (Pemenuhan)
    23 => 5,  // i. SOP
    27 => 6,  // ii. SPBE
    32 => 7,  // iii. Keterbukaan Informasi

    // Sub Kategori 3 - SDM Aparatur (Pemenuhan)
    36 => 8,  // i. Perencanaan Pegawai
    40 => 9,  // ii. Pola Mutasi
    44 => 10, // iii. Pengembangan Pegawai
    51 => 11, // iv. Penetapan Kinerja
    56 => 12, // v. Penegakan Disiplin
    58 => 13, // vi. Sistem Informasi Kepegawaian

    // Sub Kategori 4 - Penguatan Akuntabilitas (Pemenuhan)
    61 => 14, // i. Keterlibatan Pimpinan
    65 => 15, // ii. Pengelolaan Akuntabilitas

    // Sub Kategori 5 - Penguatan Pengawasan (Pemenuhan)
    75 => 16, // i. Pengendalian Gratifikasi
    78 => 17, // ii. SPIP
    83 => 18, // iii. Pengaduan Masyarakat
    91 => 19, // iv. Whistle-Blowing
    96 => 20, // v. Benturan Kepentingan

    // Sub Kategori 6 - Peningkatan Kualitas Pelayanan (Pemenuhan)
    103 => 21, // i. Standar Pelayanan
    108 => 22, // ii. Budaya Pelayanan
    114 => 23, // iii. Pengelolaan Pengaduan
    114 => 24, // iv. Penilaian Kepuasan (row sama, beda context)
    116 => 25, // v. Pemanfaatan Teknologi

    // Sub Kategori 7 - Manajemen Perubahan (Reform)
    120 => 26, // i. Komitmen dalam perubahan
    128 => 27, // ii. Komitmen Pimpinan
    130 => 28, // iii. Membangun Budaya Kerja

    // Sub Kategori 8 - Penataan Tatalaksana (Reform)
    135 => 29, // i. Peta Proses Bisnis
    137 => 30, // ii. SPBE Terintegrasi
    140 => 31, // iii. Transformasi Digital

    // Sub Kategori 9 - SDM Aparatur (Reform)
    148 => 32, // i. Kinerja Individu
    150 => 33, // ii. Assessment Pegawai
    152 => 34, // iii. Pelanggaran Disiplin

    // Sub Kategori 10 - Penguatan Akuntabilitas (Reform)
    173 => 35, // i. Meningkatnya capaian
    177 => 36, // ii. Reward and Punishment
    179 => 37, // iii. Kerangka Logis

    // Sub Kategori 11 - Penguatan Pengawasan (Reform)
    184 => 38, // i. Mekanisme Pengendalian
    197 => 39, // ii. Penanganan Pengaduan (row 197 ada 2, ini yang pertama)
    197 => 40, // iii. Penyampaian LHKPN (row 197 yang kedua)

    // Sub Kategori 12 - Peningkatan Kualitas Pelayanan (Reform)
    204 => 41, // i. Upaya Inovasi
    209 => 42, // ii. Penanganan Pengaduan Pelayanan
];

$pertanyaans = [];
$currentIndikatorRow = null;
$currentIndikatorId = null;

for ($row = 1; $row <= 222; $row++) {
    $colA = $sheet->getCell('A' . $row)->getValue();
    $colE = $sheet->getCell('E' . $row)->getValue();
    $colF = $sheet->getCell('F' . $row)->getValue();
    $colG = $sheet->getCell('G' . $row)->getValue();
    $colI = $sheet->getCell('I' . $row)->getValue();
    $colJ = $sheet->getCell('J' . $row)->getValue();

    // Cek apakah ini baris indikator
    if (isset($indikatorMapping[$colA]) && $colE && preg_match('/^(i{1,3}|iv|v|vi)\.$/i', $colE)) {
        $currentIndikatorRow = $colA;
        $currentIndikatorId = $indikatorMapping[$colA];
        continue;
    }

    // Cek apakah ini baris pertanyaan (kolom F berisi a., b., c., dst)
    if ($currentIndikatorId && $colF && preg_match('/^([a-h])\.$/', $colF, $matches)) {
        $kode = $matches[1];

        // Tentukan tipe jawaban
        $tipeJawaban = 'pilihan_ganda';
        $pilihanJawaban = null;

        if ($colJ === 'Ya/Tidak' || $colJ === 'Ya') {
            $tipeJawaban = 'ya_tidak';
            $pilihanJawaban = json_encode(['Ya', 'Tidak']);
        } elseif (preg_match('/^A\/B\/C/', $colJ)) {
            $tipeJawaban = 'pilihan_ganda';
            if ($colJ === 'A/B/C') {
                $pilihanJawaban = json_encode(['A', 'B', 'C']);
            } elseif ($colJ === 'A/B/C/D') {
                $pilihanJawaban = json_encode(['A', 'B', 'C', 'D']);
            } elseif ($colJ === 'A/B/C/D/E') {
                $pilihanJawaban = json_encode(['A', 'B', 'C', 'D', 'E']);
            }
        } elseif ($colJ === '%' || $colJ === 'Jumlah' || preg_match('/Nilai/', $colJ)) {
            $tipeJawaban = 'angka';
        }

        $pertanyaan = trim(str_replace(["\r\n", "\r", "\n"], ' ', $colG));
        $penjelasan = $colI ? trim(str_replace(["\r\n", "\r", "\n"], ' ', $colI)) : null;

        $pertanyaans[] = [
            'indikator_id' => $currentIndikatorId,
            'kode' => $kode,
            'pertanyaan' => $pertanyaan,
            'penjelasan' => $penjelasan,
            'tipe_jawaban' => $tipeJawaban,
            'pilihan_jawaban' => $pilihanJawaban,
            'urutan' => count(array_filter($pertanyaans, fn ($p) => $p['indikator_id'] === $currentIndikatorId)) + 1,
            'status' => 1,
        ];
    }
}

// Generate seeder code
echo "<?php\n\n";
echo "namespace Database\\Seeders;\n\n";
echo "use Illuminate\\Database\\Seeder;\n";
echo "use Illuminate\\Support\\Facades\\DB;\n\n";
echo "class PertanyaanSeeder extends Seeder\n";
echo "{\n";
echo "    public function run(): void\n";
echo "    {\n";
echo "        \$pertanyaans = [\n";

foreach ($pertanyaans as $p) {
    echo "            [\n";
    echo "                'indikator_id' => {$p['indikator_id']},\n";
    echo "                'kode' => '{$p['kode']}',\n";
    echo "                'pertanyaan' => " . var_export($p['pertanyaan'], true) . ",\n";
    echo "                'penjelasan' => " . var_export($p['penjelasan'], true) . ",\n";
    echo "                'tipe_jawaban' => '{$p['tipe_jawaban']}',\n";
    echo "                'pilihan_jawaban' => " . ($p['pilihan_jawaban'] ? "'{$p['pilihan_jawaban']}'" : "null") . ",\n";
    echo "                'urutan' => {$p['urutan']},\n";
    echo "                'status' => 1,\n";
    echo "                'created_at' => now(),\n";
    echo "                'updated_at' => now(),\n";
    echo "            ],\n";
}

echo "        ];\n\n";
echo "        DB::table('tm_pertanyaan')->insert(\$pertanyaans);\n";
echo "    }\n";
echo "}\n";
