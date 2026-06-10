<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RejectCustomerFromAdmin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', RejectCustomerFromAdmin::class])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'administrator' => \App\Http\Middleware\EnsureUserIsAdministrator::class,
            'reject-customer-admin' => RejectCustomerFromAdmin::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->user()?->canAccessAdminPanel()) {
                return route('admin.dashboard');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('media:clean-editor-orphans')->weeklyOn(0, '03:00');
    })->create();
