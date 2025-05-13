<?php

namespace App\Exports;

use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssignmentSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $roleIdSelected;

    public function __construct($roleIdSelected)
    {
        $this->roleIdSelected = $roleIdSelected;
    }

    public function collection()
    {
        return DB::table('assignments as a')
            ->leftJoin('users as b', 'a.user_id_to', '=', 'b.id')
            ->leftJoin('roles as c', 'a.role_to', '=', 'c.id')
            ->selectRaw('
                b.name AS userToName,
                c.name AS roleToName,
                COUNT(*) AS jumlah_tugas,
                SUM(CASE WHEN a.status = 0 THEN 1 ELSE 0 END) AS jumlah_Unfinish,
                SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) AS jumlah_On_Progress,
                SUM(CASE WHEN a.status = 2 THEN 1 ELSE 1 END) AS jumlah_selesai
            ')
            ->where('a.role_to', '>', $this->roleIdSelected)
            ->groupBy('b.id', 'b.name', 'c.id', 'c.name')
            ->havingRaw('jumlah_selesai != jumlah_tugas')
            ->orderBy('c.name')
            ->orderBy('b.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Role Name',
            'Total Assignments',
            'Progress',
            'Done'
        ];
    }
}
