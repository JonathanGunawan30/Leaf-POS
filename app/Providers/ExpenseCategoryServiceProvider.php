<?php

namespace App\Providers;

use App\Services\Implementations\ExpenseCategoryServiceImpl;
use App\Services\Interfaces\ExpenseCategoryService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ExpenseCategoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        ExpenseCategoryService::class => ExpenseCategoryServiceImpl::class
    ];
    public function provides()
    {
        return [ExpenseCategoryService::class];
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
