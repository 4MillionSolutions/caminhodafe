<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Peregrinos;
use App\Models\Perfis;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PeregrinosController extends Controller
{

    public $permissoes_liberadas = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $peregrinos = new Peregrinos();

        if ($id) {
        	$peregrinos = $peregrinos->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$peregrinos = $peregrinos->where('nome', '=', $request->input('nome'));
        }


        if (!empty($request->input('status'))){
            $peregrinos = $peregrinos->where('status', '=', $request->input('status'));
        } else {
            $peregrinos = $peregrinos->where('status', '=', 'A');
        }

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());

        $peregrinos = $peregrinos->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'peregrinos',
				'peregrinos'=> $peregrinos,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'request' => $request,
                'perfil' => (new ValidaPermissaoAcessoController())->retornaPerfil(),
                'perfis' => (new Perfis())->get(),
				'rotaIncluir' => 'incluir-peregrinos',
				'rotaAlterar' => 'alterar-peregrinos'
			);

        return view('peregrinos', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incluir(Request $request)
    {

        $metodo = $request->method();

    	if ($metodo == 'POST') {

    		$peregrinos_id = $this->salva($request);

	    	return redirect()->route('peregrinos', [ 'id' => $peregrinos_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'peregrinos',
				'request' => $request,
                'perfis' => (new Perfis())->get(),
				'rotaIncluir' => 'incluir-peregrinos',
				'rotaAlterar' => 'alterar-peregrinos'
			);

        return view('peregrinos', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {
        $peregrinos = new Peregrinos();


        $peregrinos= $peregrinos->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$peregrinos_id = $this->salva($request);

	    	return redirect()->route('peregrinos', [ 'id' => $peregrinos_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'peregrinos',
				'peregrinos'=> $peregrinos,
				'request' => $request,
                'perfis' => (new Perfis())->get(),
				'rotaIncluir' => 'incluir-peregrinos',
				'rotaAlterar' => 'alterar-peregrinos'
			);

        return view('peregrinos', $data);
    }

    public function salva($request) {
        $peregrinos = new Peregrinos();
        $alterar = false;
        if($request->input('id')) {
            $peregrinos = $peregrinos::find($request->input('id'));
            $alterar = true;

        }
        $peregrinos->nome = $request->input('nome');
        $peregrinos->endereco = $request->input('endereco');
        $peregrinos->numero = $request->input('numero');
        $peregrinos->cep = $request->input('cep');
        $peregrinos->bairro = $request->input('bairro');
        $peregrinos->cidade = $request->input('cidade');
        $peregrinos->estado = $request->input('estado');
        $peregrinos->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
        $peregrinos->email = $request->input('email');
        $peregrinos->status = $request->input('status');
        $peregrinos->save();

        $users = new User();
        $users = $users->where('email', '=', $request->input('email'))->first();
        if(empty($users)) {
            $users = new User();
        }
        $users->id = $peregrinos->id;
        $users->name = $request->input('nome');
        $users->email = $request->input('email');


        if($alterar && $request->input('senha') != '') {
            $users->perfil_acesso = $users->perfil_acesso;
            $users->password = Hash::make($request->input('senha'));

        } else {
            $users->perfil_acesso = 2;
            $users->password = Hash::make($request->input('senha'));
        }
        $users->save();

        return $peregrinos->id;



}
}
