<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token): string {
            return rtrim((string) env('FRONTEND_URL', 'http://localhost:5173'), '/')
                . '/dat-lai-mat-khau?token=' . urlencode($token)
                . '&email=' . urlencode($user->getEmailForPasswordReset());
        });
    }
}
