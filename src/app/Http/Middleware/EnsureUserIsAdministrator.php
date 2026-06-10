<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdministrator
{
    /** Chỉ type = Admin (quản trị viên), không gồm Staff. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isAdmin() || ! $user->isActive()) {
            if ($request->expectsJson()) {
                abort(403, 'Chỉ quản trị viên mới có quyền thực hiện thao tác này.');
            }

            abort(403, 'Chỉ quản trị viên mới có quyền truy cập mục này.');
        }

        return $next($request);
    }
}
