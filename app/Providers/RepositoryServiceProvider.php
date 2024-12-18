<?php

namespace App\Providers;

use App\Repository\AdminRepository;
use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PlayerRepositoryInterface::class, PlayerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
