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
            ['n_opd' => 'RSU Kota Tangsel', 'alamat' => '-'],
            ['n_opd' => 'RSUD SERUT', 'alamat' => '-'],
            ['n_opd' => 'RSUP PONAREN', 'alamat' => '-'],
            ['n_opd' => 'UPTD LABKESDA', 'alamat' => '-'],
            ['n_opd' => 'UPTD SMPN 6 TANSGEL', 'alamat' => '-'],
            ['n_opd' => 'SD 2 PONDOK JAGUNG', 'alamat' => '-'],
            ['n_opd' => 'SDN KERANGGAN', 'alamat' => '-'],
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
