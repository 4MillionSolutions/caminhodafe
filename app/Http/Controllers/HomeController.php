<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Acoes;
use App\Models\Hospedagens;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public $permissoes_liberadas = [];
    public $cidades = [
            1  => 'Águas da Prata',
            2  => 'Andradas',
            3  => 'Serra dos Lima',
            4  => 'Barra',
            5  => 'Crisólia',
            6  => 'Ouro Fino',
            7  => 'Inconfidentes',
            8  => 'Borda da Mata',
            9  => 'Tocos do Moji',
            10 => 'Estiva',
            11 => 'Consolação',
            12 => 'Paraisópolis',
            13 => 'Canta Galo',
            14 => 'Luminosa',
            15 => 'Campista',
            16 => 'Campos do Jordão',
            17 => 'Piracuama',
            18 => 'Distrito Mandu',
            19 => 'Pindamonhangaba',
            20 => 'Roseira',
        ];

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
