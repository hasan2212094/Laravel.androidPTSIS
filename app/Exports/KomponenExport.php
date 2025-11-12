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
   return Komponen::with(['userBy', 'userTo', 'workorder'])->get();
}

    public function headings(): array
    {
        return [
            'ID',
            'User By',
            'User To',
            'No WO',
            'Jenis Pekerjaan',
            'Qty',
            'Spesifikasi',
            'Keterangan',
            'Status Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
        ];
    }
/**
 * @param \App\Models\Komponen $komponen
 */
public function map($komponen): array
{
    return [
        $komponen->id,
        optional($komponen->userBy)->name ?? '-',
        optional($komponen->userTo)->name ?? '-',
        optional($komponen->workorder)->nomor ?? '-',
        $komponen->jenis_Pekerjaan ?? '-',
        $komponen->qty ?? '-',
        $komponen->spekifikasi ?? '-',
        $komponen->keterangan ?? '-',
        $komponen->status_pekerjaan == 1 ? 'Done' : 'Progress',
        $komponen->date_start ?? '-',
        $komponen->date_end ?? '-',
        $komponen->comment_done ?? '-',
    ];
}
}
