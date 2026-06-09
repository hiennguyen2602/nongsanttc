<?php

namespace App\Providers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\SettingService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
        $this->app->singleton(CartService::class);
    }

    public function boot(): void
    {
        View::composer(['store.*', 'store.layouts.*', 'store.partials.*'], function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });

        View::composer('admin.partials.sidebar', function ($view) {
            $view->with('newOrdersCount', Order::query()->new()->count());
        });

        $this->configurePasswordResetMail();
    }

    private function configurePasswordResetMail(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('admin.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('admin.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            $expire = config('auth.passwords.users.expire', 60);

            return (new MailMessage)
                ->subject('Đặt lại mật khẩu - ' . config('app.name'))
                ->greeting('Xin chào!')
                ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
                ->action('Đặt lại mật khẩu', $url)
                ->line("Liên kết đặt lại mật khẩu này sẽ hết hạn sau {$expire} phút.")
                ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.')
                ->salutation('Trân trọng, ' . config('app.name'));
        });
    }
}
