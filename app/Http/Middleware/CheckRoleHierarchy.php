<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleHierarchy
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Acesso negado. Usuário não autenticado.');
        }

        // IDs de role que devem ter acesso de general_manager
        $generalManagerRoleIds = [
            '0199fdbc-9f2a-71fe-907e-5c23fdbb5eb5', // ID original
            '0199fe22-3d44-7103-b088-63b522351b97', // Seu novo ID
        ];

        $sectorManagerRoleIds = [
            '0199fdbc-9f2f-7260-b10a-17860c6602ee',
        ];

        // Verifica através do role_id ou hasRole
        foreach ($roles as $role) {
            if ($role === 'general_manager' && in_array($user->role_id, $generalManagerRoleIds)) {
                return $next($request);
            }
            if ($role === 'sector_manager' && in_array($user->role_id, $sectorManagerRoleIds)) {
                return $next($request);
            }
            if ($role === 'driver' && $user->role_id === '0199fdbc-9f37-72e2-99e0-1f5682338f33') {
                return $next($request);
            }
            if ($role === 'garbage_manager' && $user->role_id === '0199fdbc-9f34-71f8-b7d9-b591bde8ba0d') {
                return $next($request);
            }
            if ($role === 'mechanic' && $user->role_id === '0199fdbc-9f3a-709c-b21c-8f882f03e610') {
                return $next($request);
            }

            // Verificação por nome do role (fallback)
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Acesso negado. Você não possui privilégios suficientes. Seu role: ' . $user->role_id);
    }
}
