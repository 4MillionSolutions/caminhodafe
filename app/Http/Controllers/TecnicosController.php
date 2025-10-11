<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tecnicos;

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

        if (!empty($request->input('documento'))) {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $tecnicos = $tecnicos->where('documento', '=', $documento);
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

        return view('tecnicos', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $tecnicos_id = $this->salva($request);
            return redirect()->route('tecnicos', [ 'id' => $tecnicos_id ] );
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tecnicos',
            'request' => $request,
            'rotaIncluir' => 'incluir-tecnicos',
            'rotaAlterar' => 'alterar-tecnicos'
        );

        return view('tecnicos', $data);
    }

    public function alterar(Request $request)
    {
        $tecnicos = new Tecnicos();
        $tecnicos = $tecnicos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $tecnicos_id = $this->salva($request);
            return redirect()->route('tecnicos', [ 'id' => $tecnicos_id ] );
        }

        $tecnicos = $tecnicos->get();

        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'tecnicos',
            'tecnicos'=> $tecnicos,
            'estados' => collect($this->getEstados())->toBase(),
            'request' => $request,
            'rotaIncluir' => 'incluir-tecnicos',
            'rotaAlterar' => 'alterar-tecnicos'
        );

        return view('tecnicos', $data);
    }

    public function salva($request) {


        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        if($request->input('id')) {

            $tecnicos = tecnicos::where('id', $request->input('id'))->first();
        }


        $tecnicos->nome = $request->input('nome');
        $tecnicos->ativo = $request->input('ativo');

        $tecnicos->save();

        return $tecnicos->id;
    }

    public function getAllTecnicos() {
        $tecnicos = new Tecnicos();
        $query = $tecnicos->where('ativo', '=', 1);

        return $query->get();
    }
}
