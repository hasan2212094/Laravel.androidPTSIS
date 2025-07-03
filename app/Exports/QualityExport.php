<?php

namespace App\Exports;

use App\Models\Quality;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class QualityExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters; // terima filter dari controller
    }

    public function collection()
    {
        $query = Quality::with('user');

        // Filter status
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter tanggal
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('date', [
                $this->filters['start_date'],
                $this->filters['end_date'],
            ]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Project',
            'No WO',
            'Description',
            'Responds',
            'Status',
            'Status Relevan',
            'Description Relevan',
            'Comment',
            'Date End',
            'Date',
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
            $quality->status,
            $quality->status_relevan,
            $quality->description_relevan,
            $quality->comment,
            // Pastikan aman jika date_end atau date string/null
            $quality->date_end ? \Carbon\Carbon::parse($quality->date_end)->format('Y-m-d') : '-',
            $quality->date ? \Carbon\Carbon::parse($quality->date)->format('Y-m-d') : '-',
            optional($quality->user)->name ?? '-',
        ];
    }
}
