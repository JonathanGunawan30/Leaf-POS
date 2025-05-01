<?php

namespace App\Providers;

use App\Services\Implementations\UnitServiceImpl;
use App\Services\Interfaces\UnitService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class UnitServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        UnitService::class => UnitServiceImpl::class
    ];

    public function provides()
    {
        return [UnitService::class];
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
