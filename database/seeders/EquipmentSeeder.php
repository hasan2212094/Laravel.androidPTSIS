<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $equipments = [
            // ['no_serial' => '2017-001', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-002', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-003', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-004', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-005', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-006', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-007', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-008', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-009', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-010', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-011', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-012', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-013', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-014', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-015', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-016', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-017', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-018', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-019', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-020', 'nama_alat' => 'Kind NBC 350'],
            // ['no_serial' => '2017-021', 'nama_alat' => 'Fenaweld'],
            // ['no_serial' => '2021-022', 'nama_alat' => 'Panasonic KR 350'],
            // ['no_serial' => '2021-023', 'nama_alat' => 'Panasonic KR 350'],
            // ['no_serial' => '2021-024', 'nama_alat' => 'Panasonic KR 350'],
            // ['no_serial' => '2021-025', 'nama_alat' => 'Panasonic KR 350'],
            // ['no_serial' => '2024-026', 'nama_alat' => 'Shanghai Hugong'],
            // ['no_serial' => '2024-027', 'nama_alat' => 'Shanghai Hugong'],
            // ['no_serial' => '2024-028', 'nama_alat' => 'Shanghai Hugong'],
            // ['no_serial' => '2024-029', 'nama_alat' => 'Shanghai Hugong'],
            // ['no_serial' => '2024-030', 'nama_alat' => 'Shanghai Hugong'],
            // ['no_serial' => '2017-001-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-002-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-003-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-004-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-005-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-006-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-007-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-008-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-009-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-010-ZX', 'nama_alat' => 'Kind ZX 7 400'],
            // ['no_serial' => '2017-011-Daiden', 'nama_alat' => '2017-011-Daiden'],
            // ['no_serial' => '2022-012-Lakoni', 'nama_alat' => 'Lakoni 200 A'],
            // ['no_serial' => '2025-031-Lakoni', 'nama_alat' => 'Lakoni 120 A'],
            // ['no_serial' => '2017-001-Plasma', 'nama_alat' => '-'],
            // ['no_serial' => '2017-002-Plasma', 'nama_alat' => '-'],
            // ['no_serial' => '2017-003-Plasma', 'nama_alat' => '-'],
            // ['no_serial' => '2025-004-LGK', 'nama_alat' => 'HG Shanghai Hugong LGK 100 Plus'],
            // ['no_serial' => '2025-005-LGK', 'nama_alat' => 'HG Shanghai Hugong LGK 100 Plus'],
            // ['no_serial' => '2012-001-J23', 'nama_alat' => 'J23-25 Cap 250T'],
            // ['no_serial' => '2012-002-NC', 'nama_alat' => 'China Cap 100 Ton'],
            // ['no_serial' => '2012-003-Lathe', 'nama_alat' => 'SMTCL'],
            // ['no_serial' => '2012-004-Elektrik', 'nama_alat' => 'Up To 12mm'],
            // ['no_serial' => '2014-005-Elektrik', 'nama_alat' => '2 Bar'],
            // ['no_serial' => '2014-006-Senai', 'nama_alat' => 'Senai'],
            // ['no_serial' => '2014-007-Elektrik', 'nama_alat' => 'Up To 12mm'],
            // ['no_serial' => '2014-008-Elektrik', 'nama_alat' => '4 Bar'],
            // ['no_serial' => '2019-009-Bensaw', 'nama_alat' => 'CS-280'],
            // ['no_serial' => '2021-010-Flame&Plasma', 'nama_alat' => 'Stargezer'],
            // ['no_serial' => '2023-011-L-5A', 'nama_alat' => 'L-5A'],
            // ['no_serial' => '2023-012-Z 3040', 'nama_alat' => 'Z 3040'],
            // ['no_serial' => '2023-013-shering', 'nama_alat' => 'Nan Jiang Ya Wei'],
            // ['no_serial' => '2024-014-Flame & Plasma', 'nama_alat' => 'Stargezer'],
            // ['no_serial' => '2024-015-Z 3040', 'nama_alat' => 'Z 3040'],
            // ['no_serial' => '2024-015-Elektrik', 'nama_alat' => '4 BAR'],
            // ['no_serial' => '2025-016-custom', 'nama_alat' => 'Buatan Pak Gunanto'],
            ['no_serial' => '2025-017-crain', 'nama_alat' => 'crain 01'],
            ['no_serial' => '2025-018-kompresor', 'nama_alat' => 'kompresor 01'],
        ];

        Equipment::insert($equipments);
    }
}
