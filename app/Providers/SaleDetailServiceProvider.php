<?php

namespace App\Providers;

use App\Services\Implementations\SaleDetailServiceImpl;
use App\Services\Interfaces\SaleDetailService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SaleDetailServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        SaleDetailService::class => SaleDetailServiceImpl::class
    ];
    public function provides(): array
    {
        return [SaleDetailService::class];
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
