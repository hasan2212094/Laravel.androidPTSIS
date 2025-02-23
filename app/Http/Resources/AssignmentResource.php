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
            
            'assignor' => $this->user ? $this->user->name : null,
            'assignee'=>$this->role ? $this->role->name : null,
            'name assignor' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'image' => $this->image,
            'level_urgent' => $this->level_urgent,
            'status' => $this->status,
            'description_end'=>$this->description_end,
            'date_end' => $this->date_end,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),  // Tambahan field created_at dan updated_at ke dalam response
          
            
        ];
    }
}
