<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imoveis;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables


class ImoveisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {


        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $imoveis = new Imoveis();

        if ($id) {
            $imoveis = $imoveis->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $imoveis = $imoveis->where('ativo', '=', $request->input('ativo'));
        } else {
            $imoveis = $imoveis->where('ativo', '=', '1');
        }

        if (!empty($request->input('endereco'))) {
            $imoveis = $imoveis->where('endereco', 'like', '%'.$request->input('endereco').'%');
        }

        $imoveis = $imoveis->get();

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'imoveis',
            'imoveis'=> $imoveis,
            'estados'=> $this->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-imoveis',
            'rotaAlterar' => 'alterar-imoveis'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('imoveis', $data);
    }

    public function getData(Request $request)
    {
        $imoveis = new Imoveis();

        $imoveis = $imoveis::select(['id', 'nome', 'ativo'])
                            ->where('ativo', '=', '1');

        return DataTables::of($imoveis)
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('ativo', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowId(function($row) {
                return 'cliente_' . $row->id;
            })
            ->editColumn('ativo', function ($row) {
                return $row->ativo ? 'Ativo' : 'Inativo';
            })
            ->addColumn('acoes', function ($row) {
                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_imoveis pointer"></i>'.
                        '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }



    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $imoveis = $this->salva($request);
        }
        return response()->json($imoveis->toArray());

    }

    public function alterar(Request $request)
    {
        $imoveis = new Imoveis();
        $imoveis = $imoveis->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $imoveis = $this->salva($request);
        }

        $data = [
            "id" => $imoveis->id,
            "nome" => $imoveis->nome,
            "ativo" => $imoveis->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_imoveis" data-id="'.$imoveis->id.'" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

    public function salva($request) {

        if($request->input('id')) {

            $imoveis = imoveis::where('id', $request->input('id'))->first();
        }

        $imoveis->numero = $request->input('numero');
        $imoveis->complemento = $request->input('complemento');
        $imoveis->cep = $request->input('cep');
        $imoveis->endereco = $request->input('endereco');
        $imoveis->bairro = $request->input('bairro');
        $imoveis->cidade = $request->input('cidade');
        $imoveis->estado = $request->input('estado');
        $imoveis->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
        $imoveis->contato = $request->input('contato');
        $imoveis->observacoes = $request->input('observacoes');
        $imoveis->ativo = $request->input('ativo');

        $imoveis->save();

        return $imoveis->id;
    }

    public function getEstados() {
       return [
            ['id' =>1,
            'sigla'=>'AC',
             'estado'=>'Acre',
       ],
            ['id' =>2,
            'sigla'=>'AL',
             'estado'=>'Alagoas',
       ],
            ['id' =>3,
            'sigla'=>'AP',
             'estado'=>'Amapá',
       ],
            ['id' =>4,
            'sigla'=>'AM',
             'estado'=>'Amazonas',
       ],
            ['id' =>5,
            'sigla'=>'BA',
             'estado'=>'Bahia',
       ],
            ['id' =>6,
            'sigla'=>'CE',
             'estado'=>'Ceará',
       ],
            ['id' =>7,
            'sigla'=>'DF',
             'estado'=>'Distrito Federal',
       ],
            ['id' =>8,
            'sigla'=>'ES',
             'estado'=>'Espírito Santo',
       ],
            ['id' =>9,
            'sigla'=>'GO',
             'estado'=>'Goiás',
       ],
            ['id' =>10,
            'sigla'=>'MA',
             'estado'=>'Maranhão',
       ],
       [
            'id' =>11,
            'sigla'=>'MT',
             'estado'=>'Mato Grosso',
       ],
            ['id' =>12,
            'sigla'=>'MS',
             'estado'=>'Mato Grosso do Sul',
       ],
            ['id' =>13,
            'sigla'=>'MG',
             'estado'=>'Minas Gerais',
       ],
            ['id' =>14,
            'sigla'=>'PA',
             'estado'=>'Pará',
       ],
            ['id' =>15,
            'sigla'=>'PB',
             'estado'=>'Paraíba',
       ],
            ['id' =>16,
            'sigla'=>'PR',
             'estado'=>'Paraná',
       ],
            ['id' =>17,
            'sigla'=>'PE',
             'estado'=>'Pernambuco',
       ],
            ['id' =>18,
            'sigla'=>'PI',
             'estado'=>'Piauí',
       ],
            ['id' =>19,
            'sigla'=>'RJ',
             'estado'=>'Rio de Janeiro',
       ],
            ['id' =>20,
            'sigla'=>'RN',
             'estado'=>'Rio Grande do Norte',
       ],
            ['id' =>21,
            'sigla'=>'RS',
             'estado'=>'Rio Grande do Sul',
       ],
            ['id' =>22,
            'sigla'=>'RO',
             'estado'=>'Rondônia',
       ],
            ['id' =>23,
            'sigla'=>'RR',
             'estado'=>'Roraima',
       ],
            ['id' =>24,
            'sigla'=>'SC',
             'estado'=>'Santa Catarina',
       ],
            ['id' =>25,
            'sigla'=>'SP',
             'estado'=>'São Paulo',
       ],
            ['id' =>26,
            'sigla'=>'SE',
             'estado'=>'Sergipe',
       ],
           [  'id' =>27,
           'sigla'=>'TO',
           'estado'=> 	'Tocantins'
           ]
        ];
    }

    public function getAllImoveis() {
        $imoveis = new Imoveis();
        $query = $imoveis->where('ativo', '=', 'A');

        return $query->get();
    }
}
