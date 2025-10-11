<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes(['register' => true, 'reset' => true]);

Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('afterAuth:perfis');


Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/clientes', [App\Http\Controllers\ClientesController::class, 'index'])->name('clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'],'/alterar-clientes', [App\Http\Controllers\ClientesController::class, 'alterar'])->name('alterar-clientes')->middleware('afterAuth:clientes');
Route::match(['get', 'post'],'/incluir-clientes', [App\Http\Controllers\ClientesController::class, 'incluir'])->name('incluir-clientes')->middleware('afterAuth:clientes');

Route::get('/motivos', [App\Http\Controllers\MotivosChamadosController::class, 'index'])->name('motivos')->middleware('afterAuth:motivos');
Route::match(['get', 'post'],'/alterar-motivos', [App\Http\Controllers\MotivosChamadosController::class, 'alterar'])->name('alterar-motivos')->middleware('afterAuth:motivos');
Route::match(['get', 'post'],'/incluir-motivos', [App\Http\Controllers\MotivosChamadosController::class, 'incluir'])->name('incluir-motivos')->middleware('afterAuth:motivos');

Route::get('/tecnicos', [App\Http\Controllers\TecnicosController::class, 'index'])->name('tecnicos')->middleware('afterAuth:tecnicos');
Route::match(['get', 'post'],'/alterar-tecnicos', [App\Http\Controllers\TecnicosController::class, 'alterar'])->name('alterar-tecnicos')->middleware('afterAuth:tecnicos');
Route::match(['get', 'post'],'/incluir-tecnicos', [App\Http\Controllers\TecnicosController::class, 'incluir'])->name('incluir-tecnicos')->middleware('afterAuth:tecnicos');

Route::get('/servicos', [App\Http\Controllers\ServicosController::class, 'index'])->name('servicos')->middleware('afterAuth:servicos');
Route::match(['get', 'post'],'/alterar-servicos', [App\Http\Controllers\ServicosController::class, 'alterar'])->name('alterar-servicos')->middleware('afterAuth:servicos');
Route::match(['get', 'post'],'/incluir-servicos', [App\Http\Controllers\ServicosController::class, 'incluir'])->name('incluir-servicos')->middleware('afterAuth:servicos');

Route::get('/operacoes', [App\Http\Controllers\OperacoesController::class, 'index'])->name('operacoes')->middleware('afterAuth:operacoes');
Route::match(['get', 'post'],'/alterar-operacoes', [App\Http\Controllers\OperacoesController::class, 'alterar'])->name('alterar-operacoes')->middleware('afterAuth:operacoes');
Route::match(['get', 'post'],'/incluir-operacoes', [App\Http\Controllers\OperacoesController::class, 'incluir'])->name('incluir-operacoes')->middleware('afterAuth:operacoes');

Route::get('/controle-entregas-laudos', [App\Http\Controllers\ControleEntregasLaudosController::class, 'index'])->name('controle-entregas-laudos')->middleware('afterAuth:controle-entregas-laudos');
Route::match(['get', 'post'],'/alterar-controle-entregas-laudos', [App\Http\Controllers\ControleEntregasLaudosController::class, 'alterar'])->name('alterar-controle-entregas-laudos')->middleware('afterAuth:controle-entregas-laudos');
Route::match(['get', 'post'],'/incluir-controle-entregas-laudos', [App\Http\Controllers\ControleEntregasLaudosController::class, 'incluir'])->name('incluir-controle-entregas-laudos')->middleware('afterAuth:controle-entregas-laudos');
