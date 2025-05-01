<?php

namespace App\Providers;

use App\Models\Purchase;
use App\Services\Implementations\PurchaseServiceImpl;
use App\Services\Interfaces\PurchaseService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PurchaseServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        PurchaseService::class => PurchaseServiceImpl::class
    ];

    public function provides(): array
    {
        return [PurchaseService::class];
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
