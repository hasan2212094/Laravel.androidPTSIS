<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityResource extends JsonResource
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
            'project' => $this->project,
            'no_wo' => $this->no_wo,
            'no_wo_nomor' => $this->workorder?->nomor, // relasi ke workorders.nomor
            'description' => $this->description,
            'responds' => (bool) $this->responds,
            // 'image_url' => asset('storage/' . $this->image),
            // 
            'image_urls' => $this->images->map(function ($img) {
                return asset('storage/' . $img->image_path);
            }),
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
