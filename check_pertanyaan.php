<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$total = DB::table('tm_pertanyaan')->count();
echo "Total pertanyaan: {$total}\n\n";

echo "Pertanyaan untuk indikator 41-42 (TERAKHIR):\n";
echo str_repeat('-', 80) . "\n";

$pertanyaans = DB::table('tm_pertanyaan')
    ->whereBetween('indikator_id', [41, 42])
    ->orderBy('indikator_id')
    ->orderBy('urutan')
    ->get();

foreach ($pertanyaans as $p) {
    echo "Indikator {$p->indikator_id} ({$p->kode}): {$p->tipe_jawaban}\n";
    echo "  " . substr($p->pertanyaan, 0, 70) . "...\n\n";
}

echo str_repeat('-', 80) . "\n";
echo "Total pertanyaan indikator 41-42: " . $pertanyaans->count() . "\n\n";

// Tampilkan ringkasan per indikator
echo "RINGKASAN SELURUH PERTANYAAN PER INDIKATOR:\n";
echo str_repeat('=', 80) . "\n";
$summary = DB::table('tm_pertanyaan')
    ->selectRaw('indikator_id, COUNT(*) as total')
    ->groupBy('indikator_id')
    ->orderBy('indikator_id')
    ->get();

foreach ($summary as $item) {
    echo "Indikator {$item->indikator_id}: {$item->total} pertanyaan\n";
}
echo str_repeat('=', 80) . "\n";
echo "TOTAL SEMUA PERTANYAAN: {$total}\n";
