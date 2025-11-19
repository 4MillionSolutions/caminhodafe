<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Acoes;
use App\Models\Hospedagens;
use Illuminate\Http\Request;

class HospedagensController extends Controller
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

        return view('hospedagens', $data);
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

    		$hospedagens_id = $this->salva($request);

	    	return redirect()->route('hospedagens', [ 'id' => $hospedagens_id ] );

    	}

        $acoes =new Acoes();
        $acoes = $acoes->get();

        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'hospedagens',
                'acoes' => $acoes,
                'cidades' => $this->cidades,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'request' => $request,
				'rotaIncluir' => 'incluir-hospedagens',
				'rotaAlterar' => 'alterar-hospedagens'
			);




        return view('hospedagens', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());

        $hospedagens = new Hospedagens();


        $hospedagens= $hospedagens->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$hospedagens_id = $this->salva($request);

	    	return redirect()->route('hospedagens', [ 'id' => $hospedagens_id ] );

    	}


        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'hospedagens',
				'hospedagens'=> $hospedagens,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'request' => $request,
                'cidades' => $this->cidades,
				'rotaIncluir' => 'incluir-hospedagens',
				'rotaAlterar' => 'alterar-hospedagens'
			);

        return view('hospedagens', $data);
    }

    public function salva($request) {
        $hospedagens = new Hospedagens();

        if($request->input('id')) {
            $hospedagens = $hospedagens::find($request->input('id'));
        }

        $hospedagens->nome = $request->input('nome');
        $hospedagens->cidade = $request->input('cidade');
        $hospedagens->menor_valor = Helpers::formataSalvarFloat($request->input('menor_valor'));
        $hospedagens->maior_valor = Helpers::formataSalvarFloat($request->input('maior_valor'));
        $hospedagens->valor_cafe = Helpers::formataSalvarFloat($request->input('valor_cafe'));
        $hospedagens->desconto_parceiro = Helpers::formataSalvarFloat($request->input('desconto_parceiro'));
        $hospedagens->parceiro = $request->input('parceiro');
        $hospedagens->email = $request->input('email');
        $hospedagens->telefone = Helpers::somenteNumeros($request->input('telefone'));
        $hospedagens->telefone2 = Helpers::somenteNumeros($request->input('telefone2'));
        $hospedagens->observacoes = $request->input('observacoes');
        $hospedagens->ativo = $request->input('ativo');




        $hospedagens->save();


        return $hospedagens->id;

    }
}
