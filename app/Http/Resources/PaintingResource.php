<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaintingResource extends JsonResource
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
            'user_id_by' => $this->user_id_by ? (int) $this->user_id_by : null,
            'user_by_name' => optional($this->userBy)->name,
            'jenis_Pekerjaan' => $this->jenis_Pekerjaan,
            'keterangan' => $this->keterangan,
            'qty' => $this->qty,
            'status_pekerjaan' => (int) $this->status_pekerjaan,
            'status_pekerjaan_label' => $this->getStatusLabel($this->status_pekerjaan),
            'date_start' => $this->date_start 
                ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') 
                : null,

            'date_end' => $this->date_end 
                ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') 
                : null,

            'workorder_id' => optional($this->workorder)->id !== null ? (int) $this->workorder->id : null,
            'no_wo' => optional($this->workorder)?->nomor,
            'user_id_to' => $this->user_id_to ? (int) $this->user_id_to : null,
            'user_to_name' => optional($this->userTo)->name,
            'comment_done' => $this->comment_done ?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusLabel($status): string
    {
        return match ((int)$status) {
            1 => 'Done',
            default => 'Progress',
        };
    }
    }
