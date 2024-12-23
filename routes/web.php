<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;

//Route::get('/', Home::class);
use App\Http\Controllers\ScrapingController;
use App\Http\Controllers\ScrapingProductController;
use App\Http\Controllers\ScrapingProductControllerV1;
use App\Livewire\Game;

use App\Livewire\ChunkProductLog;
use App\Livewire\ChunkSearchLog;

Route::delete('chunk-search-log', ChunkSearchLog::class, 'delete')->name('chunk-search-log');
Route::delete('chunk-product-log', ChunkProductLog::class, 'delete')->name('chunk-product-log');

Route::get('chunk-search-log', ChunkSearchLog::class, 'render')->name('chunk-search-log');
Route::get('chunk-product-log', ChunkProductLog::class, 'render')->name('chunk-product-log');

Route::get('/game', Game::class)->name('game');
Route::get('/', [ScrapingController::class, 'showForm']);
Route::post('/scraping', [ScrapingController::class, 'performScraping']);
Route::get('/product/{productId}/{vendor}', [ScrapingProductController::class, 'product']);
Route::get('/product-v1/{productId}/{vendor}', [ScrapingProductControllerV1::class, 'product']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
