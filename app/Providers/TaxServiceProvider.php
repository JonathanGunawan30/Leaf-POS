<?php

namespace App\Providers;

use App\Services\Implementations\TaxServiceImpl;
use App\Services\Interfaces\TaxService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TaxServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public array $singletons = [
        TaxService::class => TaxServiceImpl::class,
    ];

    public function provides()
    {
        return [TaxService::class];
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
