<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "VERIFIKASI DATA PERIODE\n";
echo str_repeat('=', 80) . "\n\n";

$periodes = DB::table('tm_periode')
    ->select('id', 'tahun', 'nama_periode', 'tanggal_mulai', 'tanggal_selesai', 'status', 'is_template')
    ->orderBy('tahun')
    ->orderBy('tanggal_mulai')
    ->get();

echo "Total periode: " . $periodes->count() . "\n\n";

foreach ($periodes as $periode) {
    echo "ID: {$periode->id}\n";
    echo "Tahun: {$periode->tahun}\n";
    echo "Nama: {$periode->nama_periode}\n";
    echo "Periode: {$periode->tanggal_mulai} s/d {$periode->tanggal_selesai}\n";
    echo "Status: {$periode->status}\n";
    echo "Template: " . ($periode->is_template ? 'Ya' : 'Tidak') . "\n";
    echo str_repeat('-', 80) . "\n";
}

// Highlight periode aktif
echo "\nPERIODE AKTIF (Januari 2026):\n";
echo str_repeat('=', 80) . "\n";

$aktif = DB::table('tm_periode')
    ->where('status', 'aktif')
    ->first();

if ($aktif) {
    echo "✅ {$aktif->nama_periode}\n";
    echo "   Mulai: {$aktif->tanggal_mulai}\n";
    echo "   Selesai: {$aktif->tanggal_selesai}\n";
} else {
    echo "⚠️  Tidak ada periode aktif\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ Verifikasi selesai!\n";
