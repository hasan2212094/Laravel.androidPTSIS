<?php

namespace App\Exports;

use App\Models\AfterService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AfterServiceExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle
{
    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'ASS'; // ✅ JUDUL SHEET
    }

    /**
     * Data source
     */
    public function collection()
    {
        return AfterService::with([
            'images_Progress',
            'imagesDone',
            'userBy',
            'userTo',
        ])->orderBy('id', 'desc')->get();
    }

    /**
     * Heading Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Client',
            'Jenis Kendaraan',
            'No Polisi',
            'Produk',
            'Waranti',
            'Status',
            'Images Progress',
            'Images Done',
            'Comment Progress',
            'Comment Done',
            'Date Start',
            'Date End',
        ];
    }

    /**
     * Mapping row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->client,
            $row->jenis_kendaraan,
            $row->no_polisi,
            $row->produk,
            $row->waranti ? 'Asuransi' : 'Tidak Asuransi',
            match ($row->status_pekerjaan) {
                0 => 'Pending',
                1 => 'Progress',
                2 => 'Done',
            },

            // 🔽 IMAGE PROGRESS (LINK)
            $row->images_Progress
                ->map(fn ($img) => asset('storage/' . $img->image_path))
                ->implode("\n"),

            // 🔽 IMAGE DONE (LINK)
            $row->imagesDone
                ->map(fn ($img) => asset('storage/' . $img->image_path))
                ->implode("\n"),

            $row->comment_progress,
            $row->comment_done,
            $row->date,
            $row->date_end,
        ];
    }
}
