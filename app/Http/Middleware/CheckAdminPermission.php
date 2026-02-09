<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que verifica se o admin possui a permissão exigida.
 * Super admins passam sempre.
 *
 * Uso: ->middleware('admin.permission:perm_produtos')
 */
class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            abort(403, 'Acesso negado.');
        }

        // Super admin tem acesso total
        if ($admin->is_super_admin) {
            return $next($request);
        }

        // Verifica se a permissão existe e está ativa
        if (! $admin->{$permission}) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
