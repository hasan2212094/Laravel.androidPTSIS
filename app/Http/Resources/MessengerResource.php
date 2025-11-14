<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessengerResource extends JsonResource
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
    'title' => $this->title,
    'message' => $this->message,
    'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

    ];
    }
}
