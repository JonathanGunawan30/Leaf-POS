<?php

namespace App\Providers;

use App\Services\Implementations\CourierServiceImpl;
use App\Services\Interfaces\CourierService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CourierServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        CourierService::class => CourierServiceImpl::class
    ];

    public function provides(): array
    {
        return [CourierService::class];
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
