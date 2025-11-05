<?php

namespace App\Exports;

use App\Models\Komponen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KomponenExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
{
    return Komponen::all(); // tanpa relasi dulu
}

    public function headings(): array
    {
        return [
            'ID',
            'User By',
            'User To',
            'Jenis Pekerjaan',
            'Spekifikasi',
            'Keterangan',
            'Status Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
            'No WO',
        ];
    }

    public function map($komponen): array
    {
        return [
            $komponen->id,
            optional($komponen->userBy)->name,
            optional($komponen->userTo)->name,
            $komponen->jenis_Pekerjaan,
            $komponen->spekifikasi,
            $komponen->keterangan,
            $komponen->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $komponen->date_start,
            $komponen->date_end,
            $komponen->comment_done,
            optional($komponen->workorder)->nomor,
        ];
    }
}
