<?php

namespace App\Exports;

use App\Models\Painting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaintingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
 public function collection()
  {
        return Painting::with(['userBy', 'userTo', 'workorder','unit'])->get();
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
     * @param \App\Models\Painting $painting
     */
    public function map($painting): array
    {
        return [
            $painting->id,
            optional($painting->userBy)->name ?? '-',
            optional($painting->workorder)->nomor ?? '-',
            optional($painting->workorder)->client ?? '_',
            $painting->jenis_Pekerjaan ?? '-', // pastikan nama kolom sesuai di DB
            $painting->qty ?? '-',
            optional($painting->unit)->name,
            $painting->keterangan ?? '-',
            $painting->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $painting->date_start ?? '-',
            $painting->date_end ?? '-',
            $painting->comment_done ?? '-',
        ];
    }
}
