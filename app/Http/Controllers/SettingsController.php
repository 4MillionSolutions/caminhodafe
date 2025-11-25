<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Auth;
use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
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

        //se perfil_acesso for 1, exibe todas as pessoas cadastradas, senão exibe apenas as pessoas vinculadas ao usuário logado
        dd('aqui');

        $data = array(
            'user' => $user,
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
            $user->numero = $request->input('numero');
            $user->complemento = $request->input('complemento');
            $user->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
            $user->cep = $request->input('cep');
            $user->endereco = $request->input('endereco');
            $user->bairro = $request->input('bairro');
            $user->cidade = $request->input('cidade');
            $user->estado = $request->input('estado');
            $user->ativo = $request->input('ativo');
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
