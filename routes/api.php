<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/search/{query}/{page}/{vendor}/{engineId}', [ApiController::class, 'search']);
Route::get('/product/{productId}/{vendor}/{engineId}', [ApiController::class, 'product']);
