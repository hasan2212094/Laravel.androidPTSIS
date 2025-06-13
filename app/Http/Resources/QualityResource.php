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
            'description' => $this->description,
            'responds' => $this->responds,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'image' => $this->image,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at, // hanya akan muncul jika pakai withTrashed()
        ];
    }
}
