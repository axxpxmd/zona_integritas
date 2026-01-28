<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$total = DB::table('tm_sub_pertanyaan')->count();
echo "Total sub-pertanyaan: {$total}\n\n";

echo "SUB-PERTANYAAN PER PERTANYAAN:\n";
echo str_repeat('=', 80) . "\n";

$pertanyaan_ids = [88, 89, 100, 101, 105, 106];

foreach ($pertanyaan_ids as $pertanyaan_id) {
    // Ambil info pertanyaan utama
    $pertanyaan = DB::table('tm_pertanyaan')
        ->where('id', $pertanyaan_id)
        ->first();

    if ($pertanyaan) {
        echo "Pertanyaan ID {$pertanyaan_id} (Indikator {$pertanyaan->indikator_id}, Kode: {$pertanyaan->kode}):\n";
        echo "  " . substr($pertanyaan->pertanyaan, 0, 60) . "...\n";

        // Ambil sub-pertanyaan
        $subs = DB::table('tm_sub_pertanyaan')
            ->where('pertanyaan_id', $pertanyaan_id)
            ->orderBy('urutan')
            ->get();

        foreach ($subs as $sub) {
            echo "    [{$sub->kode}] {$sub->pertanyaan} - {$sub->tipe_input} ({$sub->satuan})\n";
            if ($sub->formula) {
                echo "        Formula: {$sub->formula}\n";
            }
        }
        echo "\n";
    }
}

echo str_repeat('=', 80) . "\n";
echo "TOTAL SUB-PERTANYAAN: {$total}\n";
