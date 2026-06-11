<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const LOGIN_MAX_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 60;

    public function showLogin(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user?->canAccessAdminPanel() && $user->isActive()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && ! $user->canAccessAdminPanel()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->ensureLoginIsNotRateLimited($request);

        $remember = $request->boolean('remember');

        if ($remember) {
            $rememberMinutes = (int) config('admin.remember_duration_days', 30) * 24 * 60;
            Auth::guard()->setRememberDuration($rememberMinutes);
        }

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->loginThrottleKey($request), self::LOGIN_DECAY_SECONDS);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        $user = Auth::user();

        if (! $user->canAccessAdminPanel() || ! $user->isActive()) {
            Auth::logout();
            RateLimiter::hit($this->loginThrottleKey($request), self::LOGIN_DECAY_SECONDS);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Khách hàng không có quyền truy cập trang quản trị.']);
        }

        RateLimiter::clear($this->loginThrottleKey($request));

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showForgotPassword(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if ($user && ! $user->canAccessAdminPanel()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Khách hàng không có quyền đặt lại mật khẩu qua trang quản trị.']);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link đặt lại mật khẩu đã được gửi qua email.');
        }

        $message = match ($status) {
            Password::RESET_THROTTLED => 'Bạn vừa yêu cầu gửi link. Vui lòng đợi một lát trước khi thử lại.',
            Password::INVALID_USER => 'Không tìm thấy email trong hệ thống.',
            default => 'Không thể gửi link đặt lại mật khẩu. Vui lòng thử lại sau.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }

    public function showResetPassword(string $token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', admin_password_rule()],
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if ($user && ! $user->canAccessAdminPanel()) {
            return back()->withErrors(['email' => 'Khách hàng không có quyền đặt lại mật khẩu qua trang quản trị.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', 'Đặt lại mật khẩu thành công.')
            : back()->withErrors(['email' => 'Token không hợp lệ hoặc đã hết hạn.']);
    }

    public function showChangePassword(): View
    {
        return view('admin.auth.change-password');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', admin_password_rule()],
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update(['password' => $request->input('password')]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    private function loginThrottleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());
    }

    private function ensureLoginIsNotRateLimited(Request $request): void
    {
        $key = $this->loginThrottleKey($request);

        if (! RateLimiter::tooManyAttempts($key, self::LOGIN_MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ]),
        ]);
    }
}
