<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author->name.' '.$this->author->surname,
            'date' => $this->date,
            'slug' => $this->slug,
            'description' => $this->description,
            'total_comments' => $this->comments->count(),
            'comments' => BlogCommentResource::collection($this->comments),
        ];
    }
}
