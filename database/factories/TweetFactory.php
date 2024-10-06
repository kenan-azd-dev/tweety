<?php

namespace Database\Factories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TweetFactory extends Factory
{
    protected $model = Tweet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'body' => $this->faker->sentence(10),
            'image_path' => $this->faker->boolean(20) ? $this->faker->imageUrl() : null, // 20% chance of an image
            'likes_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
