<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!backpack_user()->hasRole('admin')) {
            throw new AuthorizationException('إجراء غير مصرح به: ليس لديك الأذونات اللازمة للوصول إلى هذا المورد.');
        }
        return $next($request);
    }
}
