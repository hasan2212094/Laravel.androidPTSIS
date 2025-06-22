<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
         Log::info('🖼️ Images relation:', ['images' => $this->images]);

    return [
        'id' => $this->id,
        'project' => $this->project,
        'no_wo' => $this->no_wo,
        'no_wo_nomor' => $this->workorder?->nomor,
        'description' => $this->description,
        'responds' => $this->responds,
        'image_urls' => $this->images
            ? $this->images->map(fn($img) => asset('storage/' . $img->image_path))->values()->all()
            : [],
        'date' => $this->date,
        'status' => $this->status,
        'created_at' => $this->created_at?->toDateTimeString(),
        'updated_at' => $this->updated_at?->toDateTimeString(),
        'deleted_at' => $this->deleted_at?->toDateTimeString(),
    ];
}
}
