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
        return Painting::with(['userBy', 'userTo', 'workorder'])->get();
  }

    public function headings(): array
    {
        return [
            'ID',
            'User By',
            'User To',
            'Jenis Pekerjaan',
            'Qty',
            'Keterangan',
            'Status Pekerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Comment Done',
            'No WO',
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
            optional($painting->userTo)->name ?? '-',
            $painting->jenis_pekerjaan ?? '-', // pastikan nama kolom sesuai di DB
            $painting->qty ?? '-',
            $painting->keterangan ?? '-',
            $painting->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $painting->date_start ?? '-',
            $painting->date_end ?? '-',
            $painting->comment_done ?? '-',
            optional($painting->workorder)->nomor ?? '-',
        ];
    }
}
