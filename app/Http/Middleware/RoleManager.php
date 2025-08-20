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

        // Convert string role to integer for comparison
        $requiredRole = match ($role) {
            'admin' => 3,
            'client' => 1,
            'record' => 3, // record is also admin level
            'department' => 2, // if department role exists
            default => null,
        };

        // Allow access if roles match
        if ($authUserRole === $requiredRole) {
            return $next($request);
        }

        // Redirect users with mismatched roles based on their actual role
        return match ($authUserRole) {
            3 => redirect()->route('dashboard'), // Admin
            1 => redirect()->route('client.parking.manage'), // Client
            default => redirect()->route('login'),
        };
    }

}
