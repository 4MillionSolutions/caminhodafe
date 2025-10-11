<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operacoes;

class OperacoesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $operacoes = new Operacoes();

        if ($id) {
            $operacoes = $operacoes->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $operacoes = $operacoes->where('ativo', '=', $request->input('ativo'));
        } else {
            $operacoes = $operacoes->where('ativo', '=', 1);
        }

        if (!empty($request->input('documento'))) {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $operacoes = $operacoes->where('documento', '=', $documento);
        }

        if (!empty($request->input('nome'))) {
            $operacoes = $operacoes->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $operacoes = $operacoes->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'operacoes',
            'operacoes'=> $operacoes,
            'request' => $request,
            'rotaIncluir' => 'incluir-operacoes',
            'rotaAlterar' => 'alterar-operacoes'
        );

        return view('operacoes', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $operacoes = $this->salva($request);
            return redirect()->route('operacoes', [ 'id' => $operacoes ] );
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'operacoes',
            'request' => $request,
            'rotaIncluir' => 'incluir-operacoes',
            'rotaAlterar' => 'alterar-operacoes'
        );

        return view('operacoes', $data);
    }

    public function alterar(Request $request)
    {
        $operacoes = new Operacoes();
        $operacoes = $operacoes->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $operacoes = $this->salva($request);
            return redirect()->route('operacoes', [ 'id' => $operacoes ] );
        }

        $operacoes = $operacoes->get();
        // dd($operacoes);
        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'operacoes',
            'operacoes'=> $operacoes,
            'estados' => collect($this->getEstados())->toBase(),
            'request' => $request,
            'rotaIncluir' => 'incluir-operacoes',
            'rotaAlterar' => 'alterar-operacoes'
        );

        return view('operacoes', $data);
    }

    public function salva($request) {


        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        if($request->input('id')) {

            $operacoes = operacoes::where('id', $request->input('id'))->first();
        }


        $operacoes->nome = $request->input('nome');
        $operacoes->ativo = $request->input('ativo');

        $operacoes->save();

        return $operacoes->id;
    }

    public function getAllOperacoes() {
        $operacoes = new Operacoes();
        $query = $operacoes->where('ativo', '=', 1);

        return $query->get();
    }
}
