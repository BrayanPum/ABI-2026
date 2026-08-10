<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        $allowedRoles = collect($roles)
            ->flatMap(fn (string $role) => explode(',', $role))
            ->map(fn (string $role) => trim($role))
            ->filter()
            ->unique()
            ->values();

        // Si no se especificaron roles, permitir acceso
        if ($allowedRoles->isEmpty()) {
            return $next($request);
        }

        // Comprobar si el usuario tiene alguno de los roles permitidos
        if (! $user->hasAnyRole($allowedRoles->toArray())) {
            return redirect('/home')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }

    protected function normalizeRole(string $role): string
    {
        return match ($role) {
            'committe_leader' => 'committee_leader',
            default => $role,
        };
    }
}
