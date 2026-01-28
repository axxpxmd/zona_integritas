<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/public/form kuesioner.xlsx';
$spreadsheet = IOFactory::load($file);

// Fokus ke Sheet LKE ZI untuk analisa detail
$sheet = $spreadsheet->getSheetByName('LKE ZI');

echo "Analisa Detail Struktur LKE ZI" . PHP_EOL;
echo str_repeat("=", 130) . PHP_EOL;
echo "Mencari pola sub-pertanyaan..." . PHP_EOL;
echo str_repeat("=", 130) . PHP_EOL . PHP_EOL;

// Baca 222 baris untuk analisa (semua data)
for ($row = 1; $row <= 222; $row++) {
    $cols = [];
    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
        $value = $sheet->getCell($col . $row)->getValue();
        if ($value !== null && $value !== '') {
            $cleanValue = str_replace(["\r\n", "\r", "\n"], ' ', $value);
            $cleanValue = substr($cleanValue, 0, 100);
            $cols[$col] = $cleanValue;
        }
    }

    if (!empty($cols)) {
        echo sprintf("Row %3d: ", $row);
        foreach ($cols as $col => $val) {
            echo $col . '=[' . $val . '] ';
        }
        echo PHP_EOL;
    }
}
