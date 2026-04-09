<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;

class DefaultUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //use Illuminate\Support\Facades\Hash;

        Staff::create([
            'nama_guru' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('123456'),
        ]);
    }
}
