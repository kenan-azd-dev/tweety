<?php

namespace App\Providers;

use App\Policies\BlockPolicy;
use App\Policies\FollowPolicy;
use App\Policies\FollowRequestPolicy;
use App\Policies\TweetPolicy;
use App\Policies\UserProfilePolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // *********************************************** //
        // ******************** GATES ******************** //
        // *********************************************** //
        // * Block Gates
        Gate::define('block', [BlockPolicy::class, 'block']);
        Gate::define('unblock', [BlockPolicy::class, 'unblock']);
        
        // * Follow Policy
        Gate::define('follow', [FollowPolicy::class, 'follow']);
        Gate::define('unfollow', [FollowPolicy::class, 'unfollow']);

        // * Follow Request Policy
        Gate::define('send', [FollowRequestPolicy::class, 'send']);
        Gate::define('cancel', [FollowRequestPolicy::class, 'cancel']);
        Gate::define('accept', [FollowRequestPolicy::class, 'accept']);
        Gate::define('decline', [FollowRequestPolicy::class, 'decline']);

        // * Tweet Policy
        Gate::define('view', [TweetPolicy::class, 'view']);
        Gate::define('update', [TweetPolicy::class, 'update']);
        Gate::define('delete', [TweetPolicy::class, 'delete']);

        // * User Policy
        Gate::define('viewProfile', [UserProfilePolicy::class, 'viewProfile']);
        
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
