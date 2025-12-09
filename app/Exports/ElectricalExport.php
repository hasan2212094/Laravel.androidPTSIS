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
            'Operator',
            'No WO',
            'Client',
            'Jenis Pekerjaan',
            'Qty',
            'Unit',
            'Keterangan',
            'Status Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
        ];
    }
    /**
     * @param \App\Models\electrical $electrical
     */
    public function map($electrical): array
    {
        return [
            $electrical->id,
            optional($electrical->userBy)->name,
            optional($electrical->workorder)->nomor,
            optional($electrical->workorder)->client,
            $electrical->jenis_Pekerjaan,
            $electrical->qty,
            optional($electrical->unit)->name,
            $electrical->keterangan,
            $electrical->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $electrical->date_start,
            $electrical->date_end,
            $electrical->comment_done,
        ];
    }
}
