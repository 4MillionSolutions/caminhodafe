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

Route::match(['get', 'post'], '/hospedagens', [App\Http\Controllers\HospedagensController::class, 'index'])->name('hospedagens')->middleware('afterAuth:hospedagens');
Route::match(['get', 'post'], '/alterar-hospedagens', [App\Http\Controllers\HospedagensController::class, 'alterar'])->name('alterar-hospedagens')->middleware('afterAuth:hospedagens');
Route::match(['get', 'post'], '/incluir-hospedagens', [App\Http\Controllers\HospedagensController::class, 'incluir'])->name('incluir-hospedagens')->middleware('afterAuth:hospedagens');

Route::match(['get', 'post'], '/peregrinos', [App\Http\Controllers\PeregrinosController::class, 'index'])->name('peregrinos');
Route::match(['get', 'post'], '/alterar-peregrinos', [App\Http\Controllers\PeregrinosController::class, 'alterar'])->name('alterar-peregrinos');
Route::match(['get', 'post'], '/incluir-peregrinos', [App\Http\Controllers\PeregrinosController::class, 'incluir'])->name('incluir-peregrinos');
