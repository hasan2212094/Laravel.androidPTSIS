<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'user_id'=>$this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
        ];
    }
}
