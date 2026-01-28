<?php

/**
 * Test Script untuk Parsing Penjelasan Pilihan Ganda
 * Run: php test_parser.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pertanyaan;

echo "========================================\n";
echo "TEST PARSER PENJELASAN PILIHAN GANDA\n";
echo "========================================\n\n";

// Ambil beberapa sample pertanyaan dengan tipe pilihan_ganda
$pertanyaans = Pertanyaan::where('tipe_jawaban', 'pilihan_ganda')
    ->take(5)
    ->get();

foreach ($pertanyaans as $index => $pertanyaan) {
    echo "Test #" . ($index + 1) . "\n";
    echo str_repeat('-', 80) . "\n";
    echo "ID: {$pertanyaan->id}\n";
    echo "Pertanyaan: {$pertanyaan->pertanyaan}\n\n";

    echo "Penjelasan Original:\n";
    echo wordwrap($pertanyaan->penjelasan, 70) . "\n\n";

    echo "Parsed Result:\n";
    $parsed = $pertanyaan->penjelasan_list;

    if (empty($parsed)) {
        echo "⚠️  GAGAL - Tidak ada hasil parsing!\n";
    } else {
        foreach ($parsed as $item) {
            echo "  [{$item['opsi']}] {$item['text']}\n";
        }
        echo "\n✅ Berhasil di-parse: " . count($parsed) . " opsi\n";
    }

    echo "\n" . str_repeat('=', 80) . "\n\n";
}

// Test dengan format berbeda
echo "\nTEST CUSTOM FORMAT:\n";
echo str_repeat('-', 80) . "\n";

$customTest = new Pertanyaan([
    'tipe_jawaban' => 'pilihan_ganda',
    'penjelasan' => 'a. Opsi pertama dengan text panjang b. Opsi kedua c. Opsi ketiga d. Opsi keempat'
]);

echo "Custom Format: " . $customTest->penjelasan . "\n\n";
echo "Parsed:\n";
foreach ($customTest->penjelasan_list as $item) {
    echo "  [{$item['opsi']}] {$item['text']}\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ Test selesai!\n";
