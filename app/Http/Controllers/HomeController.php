<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Acoes;
use App\Models\Hospedagens;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public $permissoes_liberadas = [];
    public $cidades = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->cidades = (new \App\Models\Cidades())->all()->pluck( 'nome', 'id')->toArray();

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $hospedagens = new Hospedagens();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $hospedagens = $hospedagens->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$hospedagens = $hospedagens->where('nome', 'like', '%'.$request->input('nome').'%');
        }


        $acoes =new Acoes();
        $acoes = $acoes->get();

        $hospedagens = $hospedagens->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'hospedagens',
                'acoes' => $acoes,
                'cidades' => $this->cidades,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'hospedagens'=> $hospedagens,
				'request' => $request,
				'rotaIncluir' => 'incluir-hospedagens',
				'rotaAlterar' => 'alterar-hospedagens'
			);

        return view('home', $data);
    }
}
