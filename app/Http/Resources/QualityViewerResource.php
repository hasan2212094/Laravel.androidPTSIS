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
            'id' => (int) $this->id,
            'quality_id' => (int) $this->quality_id,
            'user_id' => (int) $this->user_id,
            'name' => $this->user->name ?? '-',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
