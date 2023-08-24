<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Auth\CustomUserProvider;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('AUTH_APP_URL') . '/reset-password?token=' . $token . '&email_address=' . $user->email_address;
        });
    }
}
