<?php

namespace App\Providers;

use App\Services\Implementations\DashboardServiceImpl;
use App\Services\Interfaces\DashboardService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        DashboardService::class => DashboardServiceImpl::class,
    ];

    public function provides(): array
    {
        return [DashboardService::class];
    }
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
