<?php

namespace App\Providers;

use App\Services\Implementations\StockOpnameServiceImpl;
use App\Services\Interfaces\StockOpnameService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StockOpnameServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        StockOpnameService::class => StockOpnameServiceImpl::class,
    ];

    public function provides(): array
    {
        return [StockOpnameService::class];
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
