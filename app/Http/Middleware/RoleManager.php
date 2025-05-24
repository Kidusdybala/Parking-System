<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $authUserRole = Auth::user()->role;

        // Allow access if roles match
        switch ($role) {
            case 'admin':
                if ($authUserRole == 0) return $next($request);
                break;
            case 'client':
                if ($authUserRole == 1) return $next($request);
                break;
            case 'department':
                if ($authUserRole == 2) return $next($request);
                break;
            case 'record':
                if ($authUserRole == 3) return $next($request);
                break;

        }

        // Redirect users with mismatched roles
        return match ($authUserRole) {
            0 => redirect()->route('dashboard'),
            1 => redirect()->route('client'),
            2 => redirect()->route('department'),
            3 => redirect()->route('record'),
            default => redirect()->route('login'),
        };
    }

}
