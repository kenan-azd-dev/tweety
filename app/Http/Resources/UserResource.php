<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{


    public function toArray($request): array
    {
        if ($this->id == $request->user()->id) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'username' => $this->username,
                'birth_date' => $this->birth_date,
                'bio' => $this->bio,
                'profile_photo_path' => $this->profile_photo_path,
                'phone' => $this->phone,
                'email' => $this->email,
                'is_private' => $this->is_private,
                'followers_count' =>  $this->followers_count,
                'following_count' => $this->following_count,
                'tweets_count' => $this->tweets_count,
                'created_at' => $this->created_at,
            ];
        }
        if ($this->is_private) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'username' => $this->username,
                'profile_photo_path' => $this->profile_photo_path,
                'is_private' => $this->is_private,
                'created_at' => $this->created_at,
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'bio' => $this->bio,
            'profile_photo_path' => $this->profile_photo_path,
            'is_private' => $this->is_private,
            'followers_count' =>  $this->followers_count,
            'following_count' => $this->following_count,
            'tweets_count' => $this->tweets_count,
            'created_at' => $this->created_at,
        ];
    }
}
