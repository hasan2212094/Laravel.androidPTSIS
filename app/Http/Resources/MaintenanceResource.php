<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
  return [
    'id' => $this->id,
    'user_id_by' => $this->user_id_by !== null ? (int) $this->user_id_by : null,
    'user_by_name' => optional($this->userBy)->name,
    'name_mesin' => $this->name_mesin,
    'jenis_perbaikan' => $this->jenis_perbaikan,
    'keterangan' => $this->keterangan,

    // Status perbaikan (code + label)
    'status_perbaikan' => (int) $this->status_perbaikan,
    'status_perbaikan_label' => $this->getStatusLabel($this->status_perbaikan),

    // ✅ Tanggal mulai dan selesai (format aman)
    'date_start' => $this->date_start 
        ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') 
        : null,

    'date_end' => $this->date_end 
        ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') 
        : null,

    // Relasi equipment
    'equipment_id' => $this->equipment_id,
    'equipment' => $this->whenLoaded('equipment', function () {
        return [
            'id' => $this->equipment->id ?? null,
            'nama_alat' => $this->equipment->nama_alat ?? null,
            'no_serial' => $this->equipment->no_serial ?? null,
        ];
    }),

    // Relasi user_to
    'user_id_to' => $this->user_id_to !== null ? (int) $this->user_id_to : null,
    'user_to_name' => optional($this->userTo)->name,

    // Gambar
    'images' => collect($this->images ?? [])
        ->map(fn($img) => $img->image_path ? asset('storage/' . $img->image_path) : null)
        ->filter()
        ->values()
        ->all(),
 'images_done' => $this->images_done
    ->map(fn($img) => $img->image_path_done ? asset('storage/' . $img->image_path_done) : null)
    ->filter()
    ->values()
    ->all(),
    'comment_done' => $this->comment_done ?? null,

    // Timestamp
    'created_at' => $this->created_at 
        ? $this->created_at->format('Y-m-d H:i:s') 
        : null,
        
    'updated_at' => $this->updated_at 
        ? $this->updated_at->format('Y-m-d H:i:s') 
        : null,
];
    }

    /**
     * Helper untuk label status perbaikan.
     */
   private function getStatusLabel($status)
    {
        return match ((int)$status) {
            1 => 'Done',
            default => 'Progress',
        };
    }
}
