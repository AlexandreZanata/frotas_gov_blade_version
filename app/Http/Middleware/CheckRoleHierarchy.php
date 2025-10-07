<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleHierarchy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'Acesso negado. Usuário não possui role atribuída.');
        }

        // Check if user has any of the required roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403, 'Acesso negado. Você não possui privilégios suficientes.');
        }

        return $next($request);
    }
}

