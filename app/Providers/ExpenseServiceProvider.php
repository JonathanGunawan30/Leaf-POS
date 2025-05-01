<?php

namespace App\Providers;

use App\Services\Implementations\ExpenseServiceImpl;
use App\Services\Interfaces\ExpenseService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ExpenseServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        ExpenseService::class => ExpenseServiceImpl::class
    ];

    public function provides()
    {
        return [ExpenseService::class];
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
