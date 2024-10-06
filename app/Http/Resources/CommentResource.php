<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'tweet_id' => $this->tweet_id,
            'body' => $this->body,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
