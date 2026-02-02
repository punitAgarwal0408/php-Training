<?php
namespace Modules\Cms\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'page_id' => $this->page_id,
            'user_id' => $this->user_id,
            'author' => $this->author ? $this->author->name : null,
            'content' => $this->content,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'replies' => CommentResource::collection($this->replies),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
