<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use function Ramsey\Uuid\v1;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'admin1'],
            ['name' => 'admin2'],
            ['name' => 'admin3'],
            ['name' => 'user4'],
        ]);
    }
}
