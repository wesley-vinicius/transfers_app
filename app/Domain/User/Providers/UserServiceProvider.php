<?php


namespace App\Domain\User\Providers;

use App\Domain\User\Models\User;
use App\Domain\User\Observers\UserObserver;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Carbon\Laravel\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
