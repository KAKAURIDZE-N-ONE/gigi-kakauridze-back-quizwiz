<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'image' => url(Storage::url($this->image)),
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'total_filled' => $this->total_filled,
            'level_id' => $this->level_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'instructions' => $this->instructions,
            'questions' => $this->questions,
            'level' => $this->level,
            'categories' => $this->categories,
            'users' => $this->users,
        ];
    }
}
