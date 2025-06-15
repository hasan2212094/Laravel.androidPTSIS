<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityViewerResource extends JsonResource
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
            'quality_id' => $this->quality_id,
            'user_id' => $this->user_id,
            'name' => $this->user->name ?? '-',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
