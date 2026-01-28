<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Urutan seeding penting karena foreign key dependencies:
     * 1. Master data hierarchy (Komponen → SubPertanyaan)
     * 2. Supporting data (Periode)
     * 3. User data
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding master data hierarchy...');

        $this->call([
            // Master Data Zona Integritas (hierarchy order)
            KomponenSeeder::class,
            KategoriSeeder::class,
            SubKategoriSeeder::class,
            IndikatorSeeder::class,
            PertanyaanSeeder::class,
            SubPertanyaanSeeder::class,

            // Supporting Data
            PeriodeSeeder::class,

            // User Management
            UserSeeder::class,
        ]);

        $this->command->info('✅ Seeding completed successfully!');
    }
}
