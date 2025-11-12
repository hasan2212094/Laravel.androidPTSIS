<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectricalResource extends JsonResource
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
    'jenis_Pekerjaan' => $this->jenis_Pekerjaan,
    'keterangan' => $this->keterangan,
    'qty' => $this->qty,
    // Status perbaikan (code + label)
    'status_pekerjaan' => (int) $this->status_pekerjaan,
    'status_pekerjaan_label' => $this->getStatusLabel($this->status_pekerjaan),

    // ✅ Tanggal mulai dan selesai (format aman)
    'date_start' => $this->date_start 
        ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') 
        : null,

    'date_end' => $this->date_end 
        ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') 
        : null,

    // Relasi workorder
    'workorder_id' => optional($this->workorder)->id !== null ? (int) $this->workorder->id : null,
    'no_wo' => optional($this->workorder)?->nomor,
    'client' => optional($this->workorder)?->client,

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
