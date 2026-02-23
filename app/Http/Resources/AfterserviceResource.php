<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AfterserviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return [
    'id' => (int) $this->id,
    'user_id_by' => $this->user_id_by !== null ? (int) $this->user_id_by : null,
    'user_by_name' => optional($this->userBy)->name,
    'client' => $this->client,
    'jenis_kendaraan' => $this->jenis_kendaraan,
    'no_polisi' => $this->no_polisi,
    'no_rangka' => $this->no_rangka,
    'produk' => $this->produk,
    'waranti' => (bool) $this->waranti,
    'waranti_label' => $this->getWarantiLabel($this->waranti),
    'keterangan' => $this->keterangan,
    'status_pekerjaan' => (int) $this->status_pekerjaan,
    'status_pekerjaan_label' => $this->getStatusLabel($this->status_pekerjaan),

    // ✅ Tanggal mulai dan selesai (format aman)
    'date_start' => $this->date_start 
        ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') 
        : null,

    'date_end' => $this->date_end 
        ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') 
        : null,
    'user_id_to' => $this->user_id_to !== null ? (int) $this->user_id_to : null,
    'user_to_name' => optional($this->userTo)->name,

    // Gambar
    'images_Progress' => $this->images_progress
    ->map(fn($img) => $img->image_path ? asset('storage/' . $img->image_path) : null)
    ->filter()
    ->values()
    ->all(),

   'imagesDone' => $this->whenLoaded('imagesDone', function () {
    return $this->imagesDone
        ->map(fn ($img) => asset('storage/' . $img->image_path))
        ->values();
         }),
        
    'comment_progress' => $this->comment_progress ?? null,
    'comment_done' => $this->comment_done ?? null,
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
            2 => 'Done',
            1 => 'Progress',
            default => 'Waiting',
        };
    }
    private function getWarantiLabel($status)
    {
        return match ((int)$status) {
            1 => 'asuransi',
            default => 'tidak asuransi',
        };
    }
}
