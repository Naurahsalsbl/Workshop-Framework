<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor')->updateOrInsert(
            ['username' => 'vendor1'], // username unik
            [
                'nama_vendor' => 'Kantin Bu Sari',
                'password' => Hash::make('password123'),
            ]
        );

        DB::table('vendor')->updateOrInsert(
            ['username' => 'vendor2'], // username unik
            [
                'nama_vendor' => 'Srikana Food Walk',
                'password' => Hash::make('password123'),
            ]
        );

    }
}
