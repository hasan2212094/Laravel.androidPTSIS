<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project' => $this->project,
            'no_wo' => optional($this->workorder)->nomor, // Pastikan field ini benar
            'description' => $this->description,
            'responds' => $this->responds,
            'image_urls' => collect($this->images)
                ->map(fn($img) => asset('storage/' . $img->image_path))
                ->values()
                ->all(),
            'date' => $this->date,
            'status' => $this->status,
            'status_relevan' => $this->status_relevan,
            'comment' => $this->comment,
            'description_relevan' => $this->description_relevan,
            'image_relevan_urls' => collect($this->imagesrelevan)
                ->map(fn($img) => asset('storage/' . $img->image_path_relevan))
                ->values()
                ->all(),
            'date_end' => $this->date_end,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
        ];
    }
}
