<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaintenanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Maintenance::with(['userBy', 'userTo', 'equipment'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User By',
            'User To',
            'Nama Mesin',
            'Jenis Perbaikan',
            'Keterangan',
            'Status Perbaikan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
            'No Serial',
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->id,
            optional($maintenance->userBy)->name,
            optional($maintenance->userTo)->name,
            $maintenance->name_mesin,
            $maintenance->jenis_perbaikan,
            $maintenance->keterangan,
            $maintenance->status_perbaikan == 1 ? 'Done' : 'Progress',
            $maintenance->date_start,
            $maintenance->date_end,
            $maintenance->comment_done,
            optional($maintenance->equipment)->no_serial,
        ];
    }
}
