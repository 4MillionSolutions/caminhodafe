<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Auth;
use App\Http\Controllers\PessoasController;
use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Indicacoes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $id = \Auth::user()->id;
        $user = User::find($id);

        $logged_user_id = $id;
        $indicacoes = new Indicacoes();
        $indicacoes = $indicacoes->join('users', 'indicacoes.user_id', '=', 'users.id')
            ->join('status_indicacao', 'indicacoes.status_indicacao_id', '=', 'status_indicacao.id')
            ->select('indicacoes.*', 'users.name as user_name', 'status_indicacao.nome as status_nome');

        $indicacoes = $indicacoes->where('user_id', '=', $logged_user_id);
        $url_do_site = env('APP_URL');

        $indicacoes = $indicacoes->get();
        $user->link = $url_do_site.'/register?codigo='.$logged_user_id;
        $user->codigo_indicacao = $logged_user_id;
        $data = array(
            'user' => $user,
            'estados' => collect((new PessoasController())->getEstados())->toBase(),
        );
        return view('settings', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSettingsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
            $id = \Auth::user()->id;
    		$user = new User();
    		$user = User::find($id);
    		$user->name = $request->input('nome');
            $user->name = $request->input('nome');
            $user->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $user->documento = $documento;
            $user->data_nascimento = $request->input('data_nascimento');
            $user->genero = $request->input('genero');
            $user->numero = $request->input('numero');
            $user->complemento = $request->input('complemento');
            $user->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
            $user->cep = $request->input('cep');
            $user->endereco = $request->input('endereco');
            $user->bairro = $request->input('bairro');
            $user->cidade = $request->input('cidade');
            $user->estado = $request->input('estado');
            $user->status = $request->input('status');
            $user->chave_pix = $request->input('chave_pix');
    		$user->email = $request->input('email');
            if(!empty(trim($request->input('password')))) {
                $user->password = Hash::make(trim($request->input('password')));
            }
    		$user->save();

        return redirect()->route('settings');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $request
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingsRequest $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
