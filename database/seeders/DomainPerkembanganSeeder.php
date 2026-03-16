<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DomainPerkembangan;

class DomainPerkembanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = array_map('str_getcsv', file(database_path('data/domain.csv')));
        unset($csv[0]); // remove header

        foreach ($csv as $row) {
            DomainPerkembangan::create([
                'domain' => $row[0],
                'kelompok_usia' => $row[1],
                'indikator' => $row[2],
            ]);
        }
    }
}
