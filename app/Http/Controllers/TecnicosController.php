<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tecnicos;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables

class TecnicosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $tecnicos = new Tecnicos();

        if ($id) {
            $tecnicos = $tecnicos->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $tecnicos = $tecnicos->where('ativo', '=', $request->input('ativo'));
        } else {
            $tecnicos = $tecnicos->where('ativo', '=', 1);
        }

        if (!empty($request->input('nome'))) {
            $tecnicos = $tecnicos->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $tecnicos = $tecnicos->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tecnicos',
            'tecnicos'=> $tecnicos,
            'request' => $request,
            'rotaIncluir' => 'incluir-tecnicos',
            'rotaAlterar' => 'alterar-tecnicos'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }
        return view('tecnicos', $data);
    }


    public function getData(Request $request)
    {
        $tecnicos = new Tecnicos();
        $tecnicos = $tecnicos::select(['id', 'nome', 'crea_cau', 'ativo']);

        return DataTables::of($tecnicos)
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('crea_cau', 'like', "%{$search}%")
                        ->orWhere('ativo', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowId(function($row) {
                return 'tecnico_' . $row->id;
            })
            ->editColumn('ativo', function ($row) {
                return $row->ativo ? 'Ativo' : 'Inativo';
            })
            ->addColumn('acoes', function ($row) {
                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_tecnicos pointer"></i>'.
                        '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }




    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $tecnicos = $this->salva($request);
        }
        return response()->json($tecnicos);

    }

    public function excluir(Request $request)
    {
        info('excluir tecnico id: '.$request->input('id'));
        try{
            $tecnicos = new Tecnicos();
            $tecnicos = $tecnicos->where('id', '=', $request->input('id'));
            $tecnicos->delete();

            $data = array(
                'nome_tela' => 'tecnicos',
                'tecnicos'=> $tecnicos,
                'request' => $request,
                'rotaIncluir' => 'incluir-tecnicos',
                'rotaAlterar' => 'alterar-tecnicos'
            );
            return view('tecnicos', $data);

        } catch (\Exception $e){
            return response(['error' => 'Erro ao excluir técnico: ' . $e->getMessage()], 500);
        }

    }

    public function alterar(Request $request)
    {
        $tecnicos = new Tecnicos();
        $tecnicos = $tecnicos->select(['id', 'nome', 'crea_cau', 'ativo']);
        $tecnicos = $tecnicos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $tecnicos = $this->salva($request);
        }

        return response()->json($tecnicos);
    }

    public function salva($request) {


        $tecnicos = new Tecnicos();

        if($request->input('id')) {

            $tecnicos = $tecnicos::where('id', $request->input('id'))->first();
        }

        $tecnicos->nome = $request->input('nome');
        $tecnicos->crea_cau = $request->input('crea_cau');
        $tecnicos->ativo = !empty($request->input('ativo')) ? $request->input('ativo') : 1;

        $tecnicos->save();

        return $tecnicos;
    }

    public function getAllTecnicos() {
        $tecnicos = new Tecnicos();
        $query = $tecnicos->where('ativo', '=', 1);

        return $query->get();
    }
}
