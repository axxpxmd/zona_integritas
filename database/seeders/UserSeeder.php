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
            'nama_instansi' => '-',
            'nama_kepala' => '-',
            'jabatan_kepala' => '-',
            'nama_operator' => '-',
            'jabatan_operator' => '-',
            'email' => 'admin@tangselkota.go.id',
            'telp' => '0',
            'alamat' => '-',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'verifikator',
            'nama_instansi' => 'Inspektorat',
            'nama_kepala' => '-',
            'jabatan_kepala' => '-',
            'nama_operator' => '-',
            'jabatan_operator' => '-',
            'email' => 'verifikator@gmail.com',
            'telp' => '0',
            'alamat' => '-',
            'password' => Hash::make('password123'),
            'role' => 'verifikator',
        ]);

        User::create([
            'username' => 'verifikator_menpan',
            'nama_instansi' => 'Menteri Pendayagunaan Aparatur Negara',
            'nama_kepala' => '-',
            'jabatan_kepala' => '-',
            'nama_operator' => '-',
            'jabatan_operator' => '-',
            'email' => 'verifikator_menpan@gmail.com',
            'telp' => '0',
            'alamat' => '-',
            'password' => Hash::make('password123'),
            'role' => 'verifikator_menpan',
        ]);

        $opds = Opd::all();

        foreach ($opds as $opd) {
            $slug = \Illuminate\Support\Str::slug($opd->n_opd, '_');
            User::create([
                'opd_id' => $opd->id,
                'username' => 'operator_' . $slug,
                'nama_instansi' => $opd->n_opd,
                'nama_kepala' => 'Kepala ' . $opd->n_opd,
                'jabatan_kepala' => 'Kepala Dinas',
                'nama_operator' => 'Operator ' . $opd->n_opd,
                'jabatan_operator' => 'Staf IT',
                'email' => 'operator_' . $slug . '@tangselkota.go.id',
                'telp' => '0',
                'alamat' => $opd->alamat ?? '-',
                'password' => Hash::make('password123'),
                'role' => 'operator',
            ]);
        }
    }
}
