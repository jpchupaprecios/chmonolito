<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si NO está logueado
        if (! Auth::check()) {
            // Redirigir a /administrador/login
            return redirect('/administrador/login')
                ->with('error', 'Debes iniciar sesión como administrador.');
        }

        // Si está logueado pero NO es admin
        if (! Auth::user()->is_admin) {
            // Middleware/Admin.php
            return redirect('/administrador/login')
                ->with('error', 'No tienes privilegios de administrador.')
                ->with('showLogoutLink', true);
        }

        return $next($request);
    }
}
