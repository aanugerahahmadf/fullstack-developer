<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BuildingRepository;
use App\Repositories\RoomRepository;
use App\Repositories\CctvRepository;
use App\Repositories\ContactRepository;
use App\Repositories\StatsRepository;
use App\Repositories\ChartRepository;
use App\Services\BuildingService;
use App\Services\RoomService;
use App\Services\CctvService;
use App\Services\ContactService;
use App\Services\StatsService;
use App\Services\ChartService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(BuildingRepository::class, function ($app) {
            return new BuildingRepository($app->make(\App\Models\Building::class));
        });

        $this->app->bind(RoomRepository::class, function ($app) {
            return new RoomRepository($app->make(\App\Models\Room::class));
        });

        $this->app->bind(CctvRepository::class, function ($app) {
            return new CctvRepository($app->make(\App\Models\Cctv::class));
        });

        $this->app->bind(ContactRepository::class, function ($app) {
            return new ContactRepository($app->make(\App\Models\Contact::class));
        });

        $this->app->bind(StatsRepository::class, function ($app) {
            return new StatsRepository($app->make(\App\Models\Stats::class));
        });

        $this->app->bind(ChartRepository::class, function ($app) {
            return new ChartRepository($app->make(\App\Models\Chart::class));
        });

        // Services
        $this->app->bind(BuildingService::class, function ($app) {
            return new BuildingService($app->make(BuildingRepository::class));
        });

        $this->app->bind(RoomService::class, function ($app) {
            return new RoomService($app->make(RoomRepository::class));
        });

        $this->app->bind(CctvService::class, function ($app) {
            return new CctvService($app->make(CctvRepository::class));
        });

        $this->app->bind(ContactService::class, function ($app) {
            return new ContactService($app->make(ContactRepository::class));
        });

        $this->app->bind(StatsService::class, function ($app) {
            return new StatsService($app->make(StatsRepository::class));
        });

        $this->app->bind(ChartService::class, function ($app) {
            return new ChartService($app->make(ChartRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
