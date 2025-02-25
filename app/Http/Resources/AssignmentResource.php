<?php

namespace App\Http\Resources;

use Carbon\Carbon;

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
            'id' => $this->id,
            'user_id_by' => $this->user_id_by,
            'user_id_to' => $this->user_id_to,
            'role_by' => $this->role_by,
            'role_to' => $this->role_to,
            'user_by_name' => $this->userBy ? $this->userBy->name : null,
            'user_to_name'=>$this->userTo ? $this->userTo->name : null,
            'role_by_name' => $this->roleBy ? $this->roleBy->name : null,
            'role_to_name'=>$this->roleTo ? $this->roleTo->name : null,
            'title' => $this->title,
            'description' => $this->description,
            'date_start' => $this->date_start ? Carbon::parse($this->date_start)->format('Y-m-d H:i:s') : null,
            'level_urgent' => $this->level_urgent,
            'status' => $this->status,
            'image' => $this->image,
            'finish_note'=>$this->finish_note,
            'date_end' => $this->date_end ? Carbon::parse($this->date_end)->format('Y-m-d H:i:s') : null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),

        ];
    }
}
