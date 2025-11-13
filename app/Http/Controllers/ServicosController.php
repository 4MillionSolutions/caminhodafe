<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use Illuminate\Http\Request;
use App\Models\Servicos;
use Yajra\DataTables\Facades\DataTables;

class ServicosController extends Controller
{

    public $permissoes_liberadas = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $servicos = new Servicos();

        if ($id) {
            $servicos = $servicos->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $servicos = $servicos->where('ativo', '=', $request->input('ativo'));
        } else {
            $servicos = $servicos->where('ativo', '=', 1);
        }

        if (!empty($request->input('nome'))) {
            $servicos = $servicos->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $servicos = $servicos->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'servicos',
            'servicos'=> $servicos,
            'permissoes_liberadas' => $this->permissoes_liberadas,
            'request' => $request,
            'rotaIncluir' => 'incluir-servicos',
            'rotaAlterar' => 'alterar-servicos'
        );


        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('servicos', $data);
    }

    public function getData(Request $request)
    {
        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $servicos = new Servicos();

        $servicos = $servicos::select(['id', 'nome', 'ativo'])
                            ->where('ativo', '=', '1');

        return DataTables::of($servicos)
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('ativo', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowId(function($row) {
                return 'servico_' . $row->id;
            })
            ->editColumn('ativo', function ($row) {
                return $row->ativo ? 'Ativo' : 'Inativo';
            })
            ->addColumn('acoes', function ($row) {
                $alterar = $excluir = $duplicar ='';

                if (in_array(2, $this->permissoes_liberadas)){

                    $alterar = '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_servicos pointer habilita_editar ml-3"></i>';
                }
                if (in_array(3, $this->permissoes_liberadas)){

                    $excluir = '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }
                if (in_array(4, $this->permissoes_liberadas)){

                    $duplicar = '<i data-id="'.$row->id.'" id="duplicar" title="Duplicar" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }

                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Visualizar" class="fas fa-eye alterar_servicos desabilita_editar pointer"></i>'.
                        $alterar.
                        $excluir.
                        $duplicar;
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $servicos = $this->salva($request);
        }
        return response()->json($servicos->toArray());

    }

    public function alterar(Request $request)
    {
        $servicos = new Servicos();
        $servicos = $servicos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $servicos = $this->salva($request);
        }

        $data = [
            "id" => $servicos->id,
            "nome" => $servicos->nome_empresa,
            "ativo" => $servicos->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_servicos" data-id="'.$servicos->id.'" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

    public function excluir(Request $request)
    {
        try{
            $servicos = new Servicos();
            $servicos = $servicos->where('id', '=', $request->input('id'));
            $servicos->delete();

            $data = array(
                'nome_tela' => 'servicos',
                'servicos'=> $servicos,
                'request' => $request,
                'rotaIncluir' => 'incluir-servicos',
                'rotaAlterar' => 'alterar-servicos'
            );
            return view('servicos', $data);

        } catch (\Exception $e){
            return response(['error' => 'Erro ao excluir técnico: ' . $e->getMessage()], 500);
        }

    }

    public function salva($request) {


        $servicos = new Servicos();
        if($request->input('id')) {

            $servicos = servicos::where('id', $request->input('id'))->first();
        }

        $ativo = ($request->input('status') == 'on') ? true : false;
        $servicos->nome = $request->input('nome');
        $servicos->prioridade = $request->input('prioridade');
        $servicos->sla = $request->input('sla');
        $servicos->valor = Helpers::formatFloatValue($request->input('valor'));
        $servicos->ativo = $ativo;

        $servicos->save();

        return $servicos;
    }

    public function getAllServicos() {
        $servicos = new Servicos();
        $query = $servicos->where('ativo', '=', 1);

        return $query->get();
    }
}
