<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyAccount\IndexController as MyAccountIndexController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResultController;
use App\Http\Middleware\Admin\Admin; // <- Aquí tu middleware
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BehaviorController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ExchangeController;

Route::get('/bh/{client_session_id?}/{url?}', [BehaviorController::class, 'index'])
    ->name('bh');

/*
|--------------------------------------------------------------------------
| RUTAS DE RESET DE CONTRASEÑA
|--------------------------------------------------------------------------
*/
Route::get('/recuperar-contrasena', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/recuperar-contrasena', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

// Página de “reset” (cuando clican en el enlace)
Route::get('/recuperar-contrasena/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/recuperar-contrasena/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| RUTAS DE LOGIN / LOGOUT / REGISTER
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout-admin', [LoginController::class, 'logoutAdmin'])->name('logout.admin');


Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.perform');

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
Route::get('/api/product/{productId}/{vendor}/{engine?}/{csi?}', [ApiController::class, 'product']);
Route::get('/api/exchange', [ExchangeController::class, 'syncExchange']);
/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index']);
Route::get('/results/{csi}', [ResultController::class, 'index']);
Route::get('/product/{id}/{vendor}/{csi?}', [ProductController::class, 'index']);
Route::get('/cart', [CartController::class, 'index']);
Route::get('/checkout', [CheckoutController::class, 'index']);
Route::get('/success', [CheckoutController::class, 'success']);
Route::get('/mi-cuenta', [MyAccountIndexController::class, 'index']);

/*
|--------------------------------------------------------------------------
| RUTAS “DASHBOARD” DE USUARIO (JETSTREAM / SANCTUM, ETC.)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| RUTA DE ADMIN LOGIN (OPCIONAL)
|--------------------------------------------------------------------------
|  Si deseas un login especial para el panel admin, lo defines aquí:
|  /admin/login GET -> muestra formulario
|  /admin/login POST -> procesa login
*/
Route::get('/administrador/login', [AdminLoginController::class, 'showAdminLoginForm'])->name('administrador.login');
Route::post('/administrador/login', [AdminLoginController::class, 'adminLogin'])->name('administrador.login.perform');

/*
|--------------------------------------------------------------------------
| RUTAS DE ADMIN
|--------------------------------------------------------------------------
|  Protegemos todo lo que empiece con "/admin/" con tu middleware de Admin.
*/
Route::prefix('administrador')->middleware([
    //'auth:sanctum',
    Admin::class, // Tu middleware que redirige a /administrador/login si no es admin
])->group(function () {
    Route::get('/', function () {
        return redirect('/administrador/dashboard');
    })->name('administrador.root');

    Route::get('/dashboard', function () {
        return view('administrador.dashboard');
    })->name('administrador.dashboard');
});
