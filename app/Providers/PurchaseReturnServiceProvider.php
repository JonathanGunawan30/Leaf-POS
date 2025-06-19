<?php

namespace App\Providers;

use App\Services\Implementations\PurchaseReturnServiceImpl;
use App\Services\Interfaces\PurchaseReturnService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PurchaseReturnServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        PurchaseReturnService::class => PurchaseReturnServiceImpl::class
    ];

    public function provides(): array
    {
        return [PurchaseReturnService::class];
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
