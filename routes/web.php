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

Route::get('/drive', [App\Http\Controllers\DriveController::class, 'listFiles'])->name('drive.index');
Route::post('/drive/upload', [App\Http\Controllers\DriveController::class, 'upload'])->name('drive.upload');
Route::get('/drive/download/{id}', [App\Http\Controllers\DriveController::class, 'download'])->name('drive.download.id');
Route::get('/drive/download/{id}/{name}', [App\Http\Controllers\DriveController::class, 'download'])->name('drive.download.name');
Route::delete('/drive/delete/{id}', [App\Http\Controllers\DriveController::class, 'delete'])->name('drive.delete');

Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('afterAuth:perfis');


Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/cadastros/{aba?}/{id?}', [App\Http\Controllers\CadastrosController::class, 'index'])->name('cadastros.id'); //->middleware('afterAuth:cadastros');
Route::post('/cadastros/{aba}/{acao}', [App\Http\Controllers\CadastrosController::class, 'acao'])->name('cadastros.acao'); //->middleware('afterAuth:cadastros');
Route::post('/cadastros/{aba}/{acao}/{id}', [App\Http\Controllers\CadastrosController::class, 'acao'])->name('cadastros.buscar'); //->middleware('afterAuth:cadastros');

Route::get('/ajax/clientes', [App\Http\Controllers\ClientesController::class, 'getData'])->name('clientes.data');
Route::get('/ajax/tecnicos', [App\Http\Controllers\TecnicosController::class, 'getData'])->name('tecnicos.data');
Route::get('/ajax/servicos', [App\Http\Controllers\ServicosController::class, 'getData'])->name('servicos.data');
Route::get('/ajax/operacoes', [App\Http\Controllers\OperacoesController::class, 'getData'])->name('operacoes.data');
Route::get('/ajax/imoveis', [App\Http\Controllers\ImoveisController::class, 'getData'])->name('imoveis.data');
Route::get('/ajax/prestadores', [App\Http\Controllers\PrestadoresController::class, 'getData'])->name('prestadores.data');
Route::get('/ajax/agendamentos', [App\Http\Controllers\AgendamentosController::class, 'ajax'])->name('agendamentos.data')->middleware('auth');


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

Route::get('/agendamentos', [App\Http\Controllers\AgendamentosController::class, 'index'])->name('agendamentos')->middleware('auth');
Route::post('/agendamentos/salva', [App\Http\Controllers\AgendamentosController::class, 'salvaAgendamento'])->name('agendamentos.salva')->middleware('auth');
Route::post('/agendamentos/deletar', [App\Http\Controllers\AgendamentosController::class, 'deletaAgendamento'])->name('agendamentos.deletar')->middleware('auth');
Route::get('/agendamentos/{id}', [App\Http\Controllers\AgendamentosController::class, 'getAgendamento'])->name('agendamentos.get')->middleware('auth');
Route::post('/imoveis/salva', [App\Http\Controllers\AgendamentosController::class, 'salvaImovel'])->name('imoveis.salva')->middleware('auth');
Route::post('/imoveis/deletar', [App\Http\Controllers\AgendamentosController::class, 'deletaImovel'])->name('imoveis.deletar')->middleware('auth');
Route::get('/imoveis/{id}', [App\Http\Controllers\AgendamentosController::class, 'getImovel'])->name('imoveis.get')->middleware('auth');
Route::get('/api/imoveis-cliente/{clienteId}', [App\Http\Controllers\AgendamentosController::class, 'getImovelsByCliente'])->name('api.imoveis-cliente')->middleware('auth');
Route::get('/ajax/imoveis', [App\Http\Controllers\AgendamentosController::class, 'ajaxImoveis'])->name('ajax.imoveis')->middleware('auth');

Route::resource('controle-laudos', App\Http\Controllers\ControleLaudosController::class)->middleware('auth');
Route::get('/controle-laudos/exportar', [App\Http\Controllers\ControleLaudosController::class, 'exportar'])->name('controle-laudos.exportar')->middleware('auth');
