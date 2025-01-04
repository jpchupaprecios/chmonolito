<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // <- para Auth::attempt

class AdminLoginController extends Controller
{
    public function showAdminLoginForm()
    {
        // Por ejemplo: resources/views/admin/login.blade.php
        return view('administrador.login');
    }

    /**
     * Procesa el login de administrador.
     */
    public function adminLogin(Request $request)
    {
        // 1. Validar campos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Intentar autenticar
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Verifica si es admin
            if (Auth::user()->is_admin) {
                $request->session()->regenerate();
                // Redirige al panel de admin
                return redirect('/administrador/dashboard')->with('success', 'Bienvenido al panel de administración.');
            }

            // Si no es admin, cierra sesión y manda error
            Auth::logout();
            return redirect('/administrador/login')->withErrors([
                'email' => 'No tienes permisos de administrador.',
            ]);
        }

        // Si falla, volvemos con error
        return back()->withErrors([
            'email' => 'Credenciales inválidas para administrador.',
        ]);
    }
}
