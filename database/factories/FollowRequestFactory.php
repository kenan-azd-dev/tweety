<?php

// database/factories/FollowRequestFactory.php

namespace Database\Factories;

use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowRequestFactory extends Factory
{
    protected $model = FollowRequest::class;

    // Keep track of unique sender-receiver pairs
    protected static $existingFollowRequests = [];

    public function definition()
    {
        $sender = User::inRandomOrder()->first();
        $receiver = User::inRandomOrder()->where('id', '!=', $sender->id)->first();

        // Create a unique key for the sender-receiver pair
        $requestKey = $sender->id . '-' . $receiver->id;

        // Check if the pair already exists
        if (in_array($requestKey, self::$existingFollowRequests)) {
            return $this->definition(); // Retry with another pair
        }

        // Store this pair to prevent future duplicates
        self::$existingFollowRequests[] = $requestKey;

        return [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ];
    }
}
