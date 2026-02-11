<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LearningMaterialResource extends JsonResource
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
            'module_id' => $this->module_id,
            'module' => $this->module?->name,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'media' => $this->file_path ? config('app.url') . '/' . $this->file_path : null,
        ];
    }
}
