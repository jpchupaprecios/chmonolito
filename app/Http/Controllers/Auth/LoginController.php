<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // <- para Auth::attempt

class LoginController extends Controller
{
    public function index()
    {
        return view('pages.auth.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar login
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // Autenticado
            $request->session()->regenerate(); // Previene Session Fixation
            return redirect()->intended('/'); // o donde quieras redirigir
        }

        // Si falla, regresamos con un error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // o la ruta que quieras
    }

    public function logoutAdmin(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/administrador/login');
    }

}
