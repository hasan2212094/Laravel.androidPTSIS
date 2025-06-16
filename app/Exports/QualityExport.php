<?php

namespace App\Exports;

use App\Models\Quality;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class QualityExport implements FromCollection
{
    public function collection()
    {
        return Quality::with('user')->get(); // include relasi jika perlu
    }

    public function headings(): array
    {
        return [
            'ID',
            'Project',
            'No WO',
            'Description',
            'Responds',
            'Tanggal',
            'User',
        ];
    }

    public function map($quality): array
    {
        return [
            $quality->id,
            $quality->project,
            $quality->no_wo,
            $quality->description,
            $quality->responds ? 'Ya' : 'Tidak',
            $quality->date->format('Y-m-d'),
            optional($quality->user)->name ?? '-',
        ];
    }
}
