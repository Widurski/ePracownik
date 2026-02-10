<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Sprawdzanie roli uzytkownika
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Brak autoryzacji'], 401);
        }

        $user->load('role');

        if ($user->role->nazwa !== $role) {
            return response()->json(['error' => 'Brak uprawnień'], 403);
        }

        return $next($request);
    }
}
