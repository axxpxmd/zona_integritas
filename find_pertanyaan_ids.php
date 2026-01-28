<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$indikators = [26, 34, 35, 39, 40];

foreach ($indikators as $ind) {
    echo "Pertanyaan dengan indikator {$ind}:\n";
    $pertanyaans = DB::table('tm_pertanyaan')
        ->where('indikator_id', $ind)
        ->get(['id', 'kode', 'pertanyaan']);

    foreach ($pertanyaans as $p) {
        echo "  ID: {$p->id} (kode: {$p->kode}) - " . substr($p->pertanyaan, 0, 50) . "...\n";
    }
    echo "\n";
}
