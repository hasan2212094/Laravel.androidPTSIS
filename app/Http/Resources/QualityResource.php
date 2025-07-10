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
            'id' => (int) $this->id,
            'user_id_by' => $this->user_id_by !== null ? (int) $this->user_id_by : null,
            'user_by_name' => optional($this->userBy)->name,
            'project' => $this->project ?? '',

            'work_order_id' => optional($this->workorder)->id !== null ? (int) $this->workorder->id : null,
            'no_wo' => optional($this->workorder)->nomor ?? null,

            'description' => $this->description ?? '',
            'responds' => (bool) $this->responds,

            // 'image_urls' => collect($this->images)
            //     ->map(fn($img) => $img->image_path)
            //     ->values()
            //     ->all(),
            'image_urls' => collect($this->images ?? [])
                ->map(fn($img) => $img->image_path)
                ->values()
                ->all(),

            'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
            'user_id_to' => $this->user_id_to !== null ? (int) $this->user_id_to : null,
            'user_to_name' => optional($this->userTo)->name,

            'status' => $this->status !== null ? (int) $this->status : null,
            'status_relevan' => $this->status_relevan !== null ? (int) $this->status_relevan : null,
            'comment' => $this->comment ?? null,
            'description_relevan' => $this->description_relevan ?? null,

            // 'image_relevan_urls' => collect($this->imagesrelevan)
            //     ->map(fn($img) => $img->image_path_relevan)
            //     ->values()
            //     ->all(),
            'image_relevan_urls' => collect($this->imagesrelevan ?? [])
                ->map(fn($img) => $img->image_path_relevan)
                ->values()
                ->all(),

            'comment_done' => $this->comment_done ?? null,

            'description_progress' => $this->description_progress ?? null,

            // 'image_progress_urls' => collect($this->imagesprogress)
            //     ->map(fn($img) => $img->image_path_inprogress)
            //     ->values()
            //     ->all(),
            'image_progress_urls' => collect($this->imagesprogress ?? [])
                ->map(fn($img) => $img->image_path_inprogress)
                ->values()
                ->all(),


            'date_end' => $this->date_end ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') : null,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
        ];
    }
}
