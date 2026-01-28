<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/public/form kuesioner.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getSheetByName('LKE ZI');

// Baca penjelasan untuk indikator 11-15
$rows = [192, 193, 197];

foreach ($rows as $row) {
    $kode = $sheet->getCell("F{$row}")->getValue();
    $pertanyaan = $sheet->getCell("G{$row}")->getValue();
    $penjelasan = $sheet->getCell("I{$row}")->getValue();
    $tipe = $sheet->getCell("J{$row}")->getValue();

    echo "Row {$row} ({$kode}):\n";
    echo "Pertanyaan: {$pertanyaan}\n";
    echo "Tipe: {$tipe}\n";
    echo "Penjelasan: {$penjelasan}\n\n";
    echo str_repeat('-', 80) . "\n\n";
}
