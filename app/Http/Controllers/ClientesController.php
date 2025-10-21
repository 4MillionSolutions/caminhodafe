<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\DateHelpers;
use App\Models\Clientes;
use App\Models\Tecnicos;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables


class ClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {


        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $clientes = new Clientes();

        if ($id) {
            $clientes = $clientes->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $clientes = $clientes->where('ativo', '=', $request->input('ativo'));
        } else {
            $clientes = $clientes->where('ativo', '=', '1');
        }

        if (!empty($request->input('nome'))) {
            $clientes = $clientes->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $clientes = $clientes->get();

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'clientes',
            'clientes'=> $clientes,
            'estados'=> (new DateHelpers())->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-clientes',
            'rotaAlterar' => 'alterar-clientes'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('clientes', $data);
    }

    public function getData(Request $request)
    {
        $clientes = new Clientes();

        $clientes = $clientes::select(['id', 'nome', 'ativo'])
                            ->where('ativo', '=', '1');

        return DataTables::of($clientes)
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
                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_clientes pointer"></i>'.
                        '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }



    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $clientes = $this->salva($request);
        }
        return response()->json($clientes->toArray());

    }

    public function alterar(Request $request)
    {
        $clientes = new Clientes();
        $clientes = $clientes->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $clientes = $this->salva($request);
        }

        $data = [
            "id" => $clientes->id,
            "nome" => $clientes->nome,
            "ativo" => $clientes->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_clientes" data-id="'.$clientes->id.'" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

    public function salva($request) {

        $clientes = new Clientes();
        if($request->input('id')) {
            $clientes = $clientes::where('id', $request->input('id'))->first();
        }

        $clientes->nome = $request->input('nome');
        $clientes->ativo = !empty($request->input('ativo')) ? $request->input('ativo') : 1;
        $clientes->save();

        return $clientes;
    }



    public function getAllClientes() {
        $clientes = new Clientes();
        $query = $clientes->where('ativo', '=', '1');

        return $query->get();
    }
}
