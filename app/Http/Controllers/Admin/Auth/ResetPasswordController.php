<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * A dónde redirigir después de resetear la contraseña.
     */
    protected $redirectTo = '/'; // o donde gustes

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        // 1. Validar
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        // 2. Intentar resetear
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                // Opcional: loguear al usuario después de reset
                Auth::login($user);
            }
        );

        // 3. Revisar respuesta
        if ($response === Password::PASSWORD_RESET) {
            // exito
            return redirect('/')->with('status', __($response));
        } else {
            // fallo
            return back()->withErrors([
                'email' => __($response),
            ]);
        }
    }

}
