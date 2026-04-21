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

        $opd = Opd::where('n_opd', 'Dinas Komunikasi dan Informatika')->first();

        if ($opd) {
            User::create([
                'opd_id' => $opd->id,
                'username' => 'operator_kominfo',
                'nama_instansi' => $opd->n_opd,
                'email' => 'kominfo@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }

        $opdInspektorat = Opd::where('n_opd', 'Inspektorat')->first();

        if ($opdInspektorat) {
            User::create([
                'opd_id' => $opdInspektorat->id,
                'username' => 'operator_inspektorat',
                'nama_instansi' => $opdInspektorat->n_opd,
                'email' => 'inspektorat@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }

        $opdDishub = Opd::where('n_opd', 'Dinas Perhubungan')->first();

        if ($opdDishub) {
            User::create([
                'opd_id' => $opdDishub->id,
                'username' => 'operator_dishub',
                'nama_instansi' => $opdDishub->n_opd,
                'email' => 'dishub@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }
    }
}
