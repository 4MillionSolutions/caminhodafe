<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => False, 'reset' => false]);

Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('afterAuth:perfis');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::match(['get', 'post'], '/clientes', [App\Http\Controllers\ClientesController::class, 'index'])->name('clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'], '/alterar-clientes', [App\Http\Controllers\ClientesController::class, 'alterar'])->name('alterar-clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'], '/incluir-clientes', [App\Http\Controllers\ClientesController::class, 'incluir'])->name('incluir-clientes')->middleware('afterAuth:clientes');

Route::match(['get', 'post'], '/laudos', [App\Http\Controllers\LaudosController::class, 'index'])->name('laudos');
Route::match(['get', 'post'], '/alterar-laudos', [App\Http\Controllers\LaudosController::class, 'alterar'])->name('alterar-laudos');
Route::match(['get', 'post'], '/incluir-laudos', [App\Http\Controllers\LaudosController::class, 'incluir'])->name('incluir-laudos');
