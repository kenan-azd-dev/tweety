<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition()
    {
        $blocker = User::inRandomOrder()->first(); // Random blocker
        $blocked = User::inRandomOrder()->where('id', '!=', $blocker->id)->first(); // Ensure they're not blocking themselves

        return [
            'blocker_id' => $blocker->id,
            'blocked_id' => $blocked->id,
        ];
    }
}
