<?php

namespace App\Providers;

use App\Services\Implementations\SupplierServiceImpl;
use App\Services\Interfaces\SupplierService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SupplierServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        SupplierService::class => SupplierServiceImpl::class
    ];

    public function provides(): array
    {
    return [SupplierService::class];
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
