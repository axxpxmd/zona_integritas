<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'nama_instansi' => 'Administrator',
            'email' => 'admin@tangselkota.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $opd = Opd::where('n_opd', 'RSUD SERUT')->first();

        if ($opd) {
            User::create([
                'opd_id' => $opd->id,
                'username' => 'operator_rsud_serut',
                'nama_instansi' => $opd->n_opd,
                'email' => 'operator_rsud_serut@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }

        $opdInspektorat = Opd::where('n_opd', 'UPTD SMPN 6 TANSGEL')->first();

        if ($opdInspektorat) {
            User::create([
                'opd_id' => $opdInspektorat->id,
                'username' => 'operator_updt_smpn6',
                'nama_instansi' => $opdInspektorat->n_opd,
                'email' => 'operator_updt_smpn6@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }

        $opdDishub = Opd::where('n_opd', 'SDN KERANGGAN')->first();

        if ($opdDishub) {
            User::create([
                'opd_id' => $opdDishub->id,
                'username' => 'operator_sdn_keranggan',
                'nama_instansi' => $opdDishub->n_opd,
                'email' => 'operator_sdn_keranggan@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }
    }
}
