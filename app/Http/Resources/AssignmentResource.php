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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'document_url' => $this->document_url ? config('app.url') . '/' . $this->document_url : null,
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user_name' => $this->user->name ?? null,
        ];
    }
}
