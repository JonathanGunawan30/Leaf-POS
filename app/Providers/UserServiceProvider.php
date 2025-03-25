<?php

namespace App\Providers;

use App\Notifications\ResetPasswordNotification;
use App\Services\Implementations\UserServiceImpl;
use App\Services\Interfaces\UserService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        UserService::class => UserServiceImpl::class
    ];

    public function provides()
    {
        return [UserService::class];
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
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new ResetPasswordNotification($token))->toMail($notifiable);
        });
    }
}
