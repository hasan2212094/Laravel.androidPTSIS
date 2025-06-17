<?php

namespace App\Exports;

use App\Models\Quality;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class QualityExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Quality::with('user')->get();
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
            $quality->date ? $quality->date->format('Y-m-d') : '',
            optional($quality->user)->name ?? '-',
        ];
    }
}

