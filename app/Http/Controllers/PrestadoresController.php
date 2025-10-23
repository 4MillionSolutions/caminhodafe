<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\DateHelpers;
use App\Models\Prestadores;
use App\Models\Servicos;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables


class PrestadoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {


        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $prestadores = new Prestadores();

        if ($id) {
            $prestadores = $prestadores->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $prestadores = $prestadores->where('ativo', '=', $request->input('ativo'));
        } else {
            $prestadores = $prestadores->where('ativo', '=', '1');
        }

        if (!empty($request->input('nome'))) {
            $prestadores = $prestadores->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $prestadores = $prestadores->get();

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'prestadores',
            'prestadores'=> $prestadores,
            'estados'=> (new DateHelpers())->getEstados(),
            'servicos' => (new Servicos())->all(),
            'request' => $request,
            'rotaIncluir' => 'incluir-prestadores',
            'rotaAlterar' => 'alterar-prestadores'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('prestadores', $data);
    }

    public function getData(Request $request)
    {
        $prestadores = new Prestadores();

        $prestadores = $prestadores::select(['id', 'nome', 'ativo'])
                            ->where('ativo', '=', '1');

        return DataTables::of($prestadores)
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
                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_prestadores pointer"></i>'.
                        '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }



    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $prestadores = $this->salva($request);
        }
        return response()->json($prestadores->toArray());

    }

    public function alterar(Request $request)
    {
        $prestadores = new Prestadores();
        $prestadores = $prestadores->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $prestadores = $this->salva($request);
        }

        $data = [
            "id" => $prestadores->id,
            "nome" => $prestadores->nome,
            "ativo" => $prestadores->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_prestadores" data-id="'.$prestadores->id.'" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

    public function salva($request) {

        $prestadores = new Prestadores();
        if($request->input('id')) {
            $prestadores = $prestadores::where('id', $request->input('id'))->first();
        }

        $prestadores->nome = $request->input('nome');
        $prestadores->ativo = !empty($request->input('ativo')) ? $request->input('ativo') : 1;
        $prestadores->save();

        return $prestadores;
    }



    public function getAllPrestadores() {
        $prestadores = new Prestadores();
        $query = $prestadores->where('ativo', '=', '1');

        return $query->get();
    }
}
