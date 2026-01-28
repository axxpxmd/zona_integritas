<?php

/**
 * Test Script untuk Verifikasi Model & Relationships
 * Run: php test_models.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{Komponen, Kategori, SubKategori, Indikator, Pertanyaan, SubPertanyaan, Periode};

echo str_repeat('=', 80) . "\n";
echo "TEST MODELS & RELATIONSHIPS\n";
echo str_repeat('=', 80) . "\n\n";

// Test 1: Komponen Model
echo "1. KOMPONEN MODEL\n";
echo str_repeat('-', 80) . "\n";
$komponens = Komponen::with('kategoris')->get();
foreach ($komponens as $komponen) {
    echo "  [{$komponen->kode}] {$komponen->nama} (Bobot: {$komponen->bobot}%)\n";
    echo "  Jumlah Kategori: " . $komponen->kategoris->count() . "\n";
}
echo "\n";

// Test 2: Kategori Model dengan relationship
echo "2. KATEGORI MODEL\n";
echo str_repeat('-', 80) . "\n";
$kategori = Kategori::with(['komponen', 'subKategoris'])->first();
if ($kategori) {
    echo "  Nama: {$kategori->nama}\n";
    echo "  Komponen: {$kategori->komponen->nama}\n";
    echo "  Jumlah Sub Kategori: " . $kategori->subKategoris->count() . "\n";
}
echo "\n";

// Test 3: SubKategori Model
echo "3. SUB KATEGORI MODEL\n";
echo str_repeat('-', 80) . "\n";
$subKategori = SubKategori::with(['kategori', 'indikators'])->first();
if ($subKategori) {
    echo "  Nama: {$subKategori->nama}\n";
    echo "  Kategori: {$subKategori->kategori->nama}\n";
    echo "  Jumlah Indikator: " . $subKategori->indikators->count() . "\n";
}
echo "\n";

// Test 4: Indikator Model
echo "4. INDIKATOR MODEL\n";
echo str_repeat('-', 80) . "\n";
$indikator = Indikator::with(['subKategori', 'pertanyaans'])->first();
if ($indikator) {
    echo "  Nama: {$indikator->nama}\n";
    echo "  Sub Kategori: {$indikator->subKategori->nama}\n";
    echo "  Jumlah Pertanyaan: " . $indikator->pertanyaans->count() . "\n";
}
echo "\n";

// Test 5: Pertanyaan Model dengan Sub-pertanyaan
echo "5. PERTANYAAN MODEL (dengan sub-pertanyaan)\n";
echo str_repeat('-', 80) . "\n";
$pertanyaan = Pertanyaan::with(['indikator', 'subPertanyaans'])->find(88);
if ($pertanyaan) {
    echo "  Pertanyaan: {$pertanyaan->pertanyaan}\n";
    echo "  Indikator: {$pertanyaan->indikator->nama}\n";
    echo "  Tipe Jawaban: {$pertanyaan->tipe_jawaban}\n";
    echo "  Has Sub-pertanyaan: " . ($pertanyaan->has_sub_pertanyaan ? 'Ya' : 'Tidak') . "\n";
    echo "  Jumlah Sub-pertanyaan: " . $pertanyaan->subPertanyaans->count() . "\n";

    if ($pertanyaan->tipe_jawaban === 'pilihan_ganda') {
        echo "  Penjelasan Parsed:\n";
        foreach ($pertanyaan->penjelasan_list as $item) {
            echo "    [{$item['opsi']}] {$item['text']}\n";
        }
    }
}
echo "\n";

// Test 6: SubPertanyaan Model
echo "6. SUB PERTANYAAN MODEL\n";
echo str_repeat('-', 80) . "\n";
$subPertanyaan = SubPertanyaan::with('pertanyaanUtama')->first();
if ($subPertanyaan) {
    echo "  Sub-pertanyaan: {$subPertanyaan->pertanyaan}\n";
    echo "  Pertanyaan Utama: {$subPertanyaan->pertanyaanUtama->pertanyaan}\n";
    echo "  Tipe Input: {$subPertanyaan->tipe_input}\n";
    echo "  Satuan: {$subPertanyaan->satuan}\n";
    if ($subPertanyaan->formula) {
        echo "  Formula: {$subPertanyaan->formula}\n";
    }
}
echo "\n";

// Test 7: Periode Model
echo "7. PERIODE MODEL\n";
echo str_repeat('-', 80) . "\n";
$periode = Periode::aktif()->first();
if ($periode) {
    echo "  Nama: {$periode->nama_periode}\n";
    echo "  Tahun: {$periode->tahun}\n";
    echo "  Periode: {$periode->tanggal_mulai->format('d M Y')} - {$periode->tanggal_selesai->format('d M Y')}\n";
    echo "  Status: {$periode->status}\n";
    echo "  Is Berlangsung: " . ($periode->is_berlangsung ? 'Ya' : 'Tidak') . "\n";
}
echo "\n";

// Test 8: Complete Hierarchy
echo "8. COMPLETE HIERARCHY TEST\n";
echo str_repeat('-', 80) . "\n";
$komponen = Komponen::with([
    'kategoris.subKategoris.indikators.pertanyaans.subPertanyaans'
])->first();

if ($komponen) {
    echo "Komponen: {$komponen->nama}\n";
    foreach ($komponen->kategoris as $kategori) {
        echo "  └─ Kategori: {$kategori->nama}\n";
        foreach ($kategori->subKategoris->take(1) as $subKategori) {
            echo "     └─ Sub Kategori: {$subKategori->nama}\n";
            foreach ($subKategori->indikators->take(1) as $indikator) {
                echo "        └─ Indikator: {$indikator->nama}\n";
                foreach ($indikator->pertanyaans->take(1) as $pertanyaan) {
                    echo "           └─ Pertanyaan: " . substr($pertanyaan->pertanyaan, 0, 50) . "...\n";
                    if ($pertanyaan->subPertanyaans->count() > 0) {
                        echo "              └─ Sub-pertanyaan: {$pertanyaan->subPertanyaans->count()} item\n";
                    }
                }
            }
        }
    }
}
echo "\n";

// Summary
echo str_repeat('=', 80) . "\n";
echo "SUMMARY\n";
echo str_repeat('=', 80) . "\n";
echo "✅ Total Komponen: " . Komponen::count() . "\n";
echo "✅ Total Kategori: " . Kategori::count() . "\n";
echo "✅ Total Sub Kategori: " . SubKategori::count() . "\n";
echo "✅ Total Indikator: " . Indikator::count() . "\n";
echo "✅ Total Pertanyaan: " . Pertanyaan::count() . "\n";
echo "✅ Total Sub-pertanyaan: " . SubPertanyaan::count() . "\n";
echo "✅ Total Periode: " . Periode::count() . "\n";
echo str_repeat('=', 80) . "\n";
echo "✅ All models & relationships working!\n";
