<?php

namespace App\Http\Controllers;

use App\Models\Agendamentos;
use Illuminate\Http\Request;
use App\Models\Producao;

class ProducaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $producao = new Agendamentos();

        if ($id) {
            $producao = $producao->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $producao = $producao->where('ativo', '=', $request->input('ativo'));
        } else {
            $producao = $producao->where('ativo', '=', 1);
        }

        if (!empty($request->input('nome'))) {
            $producao = $producao->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $producao = $producao->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'produções',
            'producao'=> $producao,
            'request' => $request,
            'rotaIncluir' => 'incluir-producao',
            'rotaAlterar' => 'alterar-producao'
        );

        return view('producao', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $producao_id = $this->salva($request);
            return redirect()->route('producao', [ 'id' => $producao_id ] );
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'producao',
            'request' => $request,
            'rotaIncluir' => 'incluir-producao',
            'rotaAlterar' => 'alterar-producao'
        );

        return view('producao', $data);
    }

    public function alterar(Request $request)
    {
        $producao = new Agendamentos();
        $producao = $producao->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $producao_id = $this->salva($request);
            return redirect()->route('producao', [ 'id' => $producao_id ] );
        }

        $producao = $producao->get();
        // dd($producao);
        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'producao',
            'producao'=> $producao,
            'estados' => collect($this->getEstados())->toBase(),
            'request' => $request,
            'rotaIncluir' => 'incluir-producao',
            'rotaAlterar' => 'alterar-producao'
        );

        return view('producao', $data);
    }

    public function salva($request) {


        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        if($request->input('id')) {

            $producao = producao::where('id', $request->input('id'))->first();
        }


        $producao->nome = $request->input('nome');
        $producao->ativo = $request->input('ativo');

        $producao->save();

        return $producao->id;
    }

    public function getAllAgendamentos() {
        $producao = new Agendamentos();
        $query = $producao->where('ativo', '=', 1);

        return $query->get();
    }
}
