<?php

namespace App\Providers;

use App\Services\Implementations\SaleServiceImpl;
use App\Services\Interfaces\SaleService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SaleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        SaleService::class => SaleServiceImpl::class
    ];

    public function provides(): array
    {
        return [SaleService::class];
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
