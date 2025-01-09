<?php

namespace App\Providers;

use App\Repository\AdminRepository;
use App\Repository\CoachMatchStatsRepository;
use App\Repository\CoachRepository;
use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\CoachMatchStatsRepositoryInterface;
use App\Repository\Interface\CoachRepositoryInterface;
use App\Repository\Interface\LeagueStandingRepositoryInterface;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\Interface\TeamRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\LeagueStandingRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
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
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(CoachRepositoryInterface::class, CoachRepository::class);
        $this->app->bind(CoachMatchStatsRepositoryInterface::class, CoachMatchStatsRepository::class);
        $this->app->bind(LeagueStandingRepositoryInterface::class, LeagueStandingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
