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

        $opds = Opd::all();

        foreach ($opds as $opd) {
            $slug = \Illuminate\Support\Str::slug($opd->n_opd, '_');
            User::create([
                'opd_id' => $opd->id,
                'username' => 'operator_' . $slug,
                'nama_instansi' => $opd->n_opd,
                'email' => 'operator_' . $slug . '@tangselkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]);
        }
    }
}
