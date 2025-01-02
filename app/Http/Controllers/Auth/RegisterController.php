<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;  // para Auth::login()
use App\Models\User;                  // tu modelo de usuario

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.register.index');
    }

    public function store(Request $request)
    {
        // Validar
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        // Crear el usuario
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        // Encriptar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        // Loguear automáticamente
        Auth::login($user);

        // Redirigir a la home (o adonde gustes)
        return redirect('/');
    }
}
