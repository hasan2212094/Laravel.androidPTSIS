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
        return Fabrikasi::withTrashed()->get();
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
            'Softdelete',
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
            optional($fabrikasi->workorder)->nomor ?? '-',
            optional($fabrikasi->workorder)->client ?? '-',
            $fabrikasi->jenis_Pekerjaan ?? '-', // pastikan nama kolom sesuai di DB
            $fabrikasi->qty ?? '-',
            optional($fabrikasi->unit)->name,
            $fabrikasi->keterangan ?? '-',
            $fabrikasi->status_pekerjaan == 1 ? 'Done' : 'Progress',
            $fabrikasi->date_start ?? '-',
            $fabrikasi->date_end ?? '-',
            $fabrikasi->comment_done ?? '-',
            $fabrikasi->deleted_at ? 'Deleted' : 'Active',
        ];
    }
}
