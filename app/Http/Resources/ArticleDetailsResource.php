<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'magazine_name' => $this->magazine->name,
            'article_id' => $this->id,
            'article_title' =>$this->title,
            'article_content' => $this->content,
            'publish_date' => $this->publish_date,
            'comments' => CommentResource::collection($this->comments)
        ];
    }
}
