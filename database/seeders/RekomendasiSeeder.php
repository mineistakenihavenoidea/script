<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rekomendasi;

class RekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = array_map('str_getcsv', file(database_path('data/rekom.csv')));
        unset($csv[0]); // remove header

        foreach ($csv as $row) {
            Rekomendasi::create([
                'jenis_rekomendasi' => $row[0],
                'kelompok_usia' => $row[1],
                'nama_rekomendasi' => $row[2],
            ]);
        }
    }
}
