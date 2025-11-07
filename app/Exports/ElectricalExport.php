<?php

namespace App\Exports;

namespace App\Exports;

use App\Models\Electrical;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ElectricalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
{
    return Electrical::all(); // tanpa relasi dulu
}

    public function headings(): array
    {
        return [
            'ID',
            'User By',
            'User To',
            'Jenis Pekerjaan',
            'Keterangan',
            'Status Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
            'No WO',
        ];
    }

    public function map($electrical): array
    {
        return [
            $electrical->id,
            optional($electrical->userBy)->name,
            optional($electrical->userTo)->name,
            $electrical->jenis_Pekerjaan,
            $electrical->keterangan,
            $electrical->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $electrical->date_start,
            $electrical->date_end,
            $electrical->comment_done,
            optional($electrical->workorder)->nomor,
        ];
    }
}
