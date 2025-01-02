<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    // Este trait incluye métodos como "sendResetLinkEmail()"
    use SendsPasswordResetEmails;

    /**
     * Muestra el formulario donde el usuario ingresa su email.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
        // O la vista Blade que uses (ej: 'pages.auth.forgot.index')
    }
}
