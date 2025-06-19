<?php

namespace App\Providers;

use App\Services\Implementations\SaleReturnServiceImpl;
use App\Services\Interfaces\SaleReturnService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SaleReturnServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        SaleReturnService::class => SaleReturnServiceImpl::class
    ];

    public function provides(): array
    {
        return [SaleReturnService::class];
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
