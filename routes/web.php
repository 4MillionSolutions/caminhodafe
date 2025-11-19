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

Auth::routes(['register' => true, 'reset' => true]);


Route::get('/google/auth', function () {
    $client = new GoogleClient();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setRedirectUri(route('google.callback'));
    $client->addScope(Google\Service\Drive::DRIVE); // acesso apenas aos arquivos do usuário
    $client->setAccessType('offline'); // para pegar refresh token
    $client->setPrompt('consent'); // garante que o refresh token seja retornado

    return redirect($client->createAuthUrl());
})->name('google.auth');

Route::get('/google/callback', function (\Illuminate\Http\Request $request) {
    $client = new GoogleClient();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setRedirectUri(route('google.callback'));

    $token = $client->fetchAccessTokenWithAuthCode($request->code);

    // Salvar tokens em database ou arquivo seguro
    Storage::put('google_tokens.json', json_encode($token));

    return "Autorização concluída!";
})->name('google.callback');


Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('afterAuth:perfis');


Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::match(['get', 'post'], '/hospedagens', [App\Http\Controllers\HospedagensController::class, 'index'])->name('hospedagens')->middleware('afterAuth:hospedagens');
Route::match(['get', 'post'], '/alterar-hospedagens', [App\Http\Controllers\HospedagensController::class, 'alterar'])->name('alterar-hospedagens')->middleware('afterAuth:hospedagens');
Route::match(['get', 'post'], '/incluir-hospedagens', [App\Http\Controllers\HospedagensController::class, 'incluir'])->name('incluir-hospedagens')->middleware('afterAuth:hospedagens');

