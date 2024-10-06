<?php
namespace Database\Seeders;

use App\Models\Block;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use App\Models\Follow;
use App\Models\FollowRequest;
use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(100)->create();

        Follow::factory(1000)->create();
        
        FollowRequest::factory(200)->create();
        
        Tweet::factory(500)->create();
        Like::factory(10000)->create();
        Comment::factory(5000)->create();

        Block::factory(80)->create();


        $this->updateCounts();
    }

    private function updateCounts()
    {
        // Update follower counts
        User::all()->each(function (User $user) {
            $user->update([
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
            ]);
        });

        // Update tweet like counts
        Tweet::all()->each(function (Tweet $tweet) {
            $tweet->update([
                'likes_count' => $tweet->likes()->count(),
                'comments_count' => $tweet->comments()->count(),
            ]);
        });
    }
}
