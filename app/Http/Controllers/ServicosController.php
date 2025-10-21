<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicos;

class ServicosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

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

        if (!empty($request->input('documento'))) {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $servicos = $servicos->where('documento', '=', $documento);
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
            'request' => $request,
            'rotaIncluir' => 'incluir-servicos',
            'rotaAlterar' => 'alterar-servicos'
        );

        return view('servicos', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $servicos_id = $this->salva($request);
            return redirect()->route('servicos', [ 'id' => $servicos_id ] );
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'servicos',
            'request' => $request,
            'rotaIncluir' => 'incluir-servicos',
            'rotaAlterar' => 'alterar-servicos'
        );

        return view('servicos', $data);
    }

    public function alterar(Request $request)
    {
        $servicos = new Servicos();
        $servicos = $servicos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $servicos_id = $this->salva($request);
            return redirect()->route('servicos', [ 'id' => $servicos_id ] );
        }

        $servicos = $servicos->get();
        // dd($servicos);
        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'servicos',
            'servicos'=> $servicos,
            'estados' => collect($this->getEstados())->toBase(),
            'request' => $request,
            'rotaIncluir' => 'incluir-servicos',
            'rotaAlterar' => 'alterar-servicos'
        );

        return view('servicos', $data);
    }

    public function salva($request) {


        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        if($request->input('id')) {

            $servicos = servicos::where('id', $request->input('id'))->first();
        }


        $servicos->nome = $request->input('nome');
        $servicos->ativo = $request->input('ativo');

        $servicos->save();

        return $servicos->id;
    }

    public function getAllServicos() {
        $servicos = new Servicos();
        $query = $servicos->where('ativo', '=', 1);

        return $query->get();
    }
}
