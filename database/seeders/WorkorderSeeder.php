<?php

namespace Database\Seeders;

use App\Models\Workorder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkorderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Workorder::insert([
            ['nomor' => 'WO-2025001'],
            ['nomor' => 'WO-2025002'],
            ['nomor' => 'WO-2025003'],
            ['nomor' => 'WO-2025004'],
            ['nomor' => 'WO-2025005'],
        ]);
    }
}
