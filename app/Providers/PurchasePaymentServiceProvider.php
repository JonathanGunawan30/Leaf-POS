<?php

namespace App\Providers;

use App\Services\Implementations\PurchasePaymentServiceImpl;
use App\Services\Interfaces\PurchasePaymentService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PurchasePaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        PurchasePaymentService::class => PurchasePaymentServiceImpl::class
    ];
    public function provides(): array
    {
        return [PurchasePaymentService::class];
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
