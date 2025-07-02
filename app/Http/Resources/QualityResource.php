<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'user_id_by' => $this->user_id_by,
            'user_id_to' => $this->user_id_to,
            'user_by_name' => $this->userBy ? $this->userBy->name : null,
            'user_to_name' => $this->userTo ? $this->userTo->name : null,
            'project' => $this->project,
            'work_order_id' => optional($this->workorder)->id,
            'no_wo' => optional($this->workorder)->nomor, // Pastikan field ini benar
            'description' => $this->description,
            'responds' => $this->responds,
            'image_urls' => collect($this->images)
                //->map(fn($img) => asset('storage/' . $img->image_path))
                ->map(fn($img) => $img->image_path)
                ->values()
                ->all(),
            'date' => $this->date ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            'status_relevan' => $this->status_relevan,
            'comment' => $this->comment,
            'description_relevan' => $this->description_relevan,
            'image_relevan_urls' => collect($this->imagesrelevan)
                //->map(fn($img) => asset('storage/' . $img->image_path_relevan))
                ->map(fn($img) => $img->image_path_relevan)
                ->values()
                ->all(),
            'date_end' => $this->date_end ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') : null,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
        ];
    }
}
