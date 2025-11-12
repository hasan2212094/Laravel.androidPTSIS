<?php

namespace App\Exports;

use App\Models\Fabrikasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FabrikasiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
 public function collection()
    {
        return Fabrikasi::with(['userBy', 'userTo', 'workorder'])->get();
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
     * @param \App\Models\Fabrikasi $fabrikasi
     */
    public function map($fabrikasi): array
    {
        return [
            $fabrikasi->id,
            optional($fabrikasi->userBy)->name ?? '-',
            optional($fabrikasi->userTo)->name ?? '-',
            $fabrikasi->jenis_pekerjaan ?? '-', // pastikan nama kolom sesuai di DB
            $fabrikasi->qty ?? '-',
            $fabrikasi->keterangan ?? '-',
            $fabrikasi->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $fabrikasi->date_start ?? '-',
            $fabrikasi->date_end ?? '-',
            $fabrikasi->comment_done ?? '-',
            optional($fabrikasi->workorder)->nomor ?? '-',
        ];
    }
}
