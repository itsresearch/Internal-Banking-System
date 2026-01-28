<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $role = $user->role ? $user->role->name : null;

            if ($role === 'admin') {
                return redirect()->route('dashboard.admin');
            } elseif ($role === 'manager') {
                return redirect()->route('dashboard.manager');
            } elseif ($role === 'staff') {
                return redirect()->route('dashboard.staff');
            }
        }

        return $next($request);
    }
}
