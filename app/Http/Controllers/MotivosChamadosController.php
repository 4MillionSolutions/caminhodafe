<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motivos;

class MotivosChamadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $motivos = new Motivos();

        if ($id) {
            $motivos = $motivos->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $motivos = $motivos->where('ativo', '=', $request->input('ativo'));
        } else {
            $motivos = $motivos->where('ativo', '=', 1);
        }

        if (!empty($request->input('documento'))) {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $motivos = $motivos->where('documento', '=', $documento);
        }

        if (!empty($request->input('nome'))) {
            $motivos = $motivos->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $motivos = $motivos->get();
        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'motivos',
            'motivos'=> $motivos,
            'request' => $request,
            'rotaIncluir' => 'incluir-motivos',
            'rotaAlterar' => 'alterar-motivos'
        );

        return view('motivos', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $motivos_id = $this->salva($request);
            return redirect()->route('motivos', [ 'id' => $motivos_id ] );
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'motivos',
            'request' => $request,
            'rotaIncluir' => 'incluir-motivos',
            'rotaAlterar' => 'alterar-motivos'
        );

        return view('motivos', $data);
    }

    public function alterar(Request $request)
    {
        $motivos = new Motivos();
        $motivos = $motivos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $motivos_id = $this->salva($request);
            return redirect()->route('motivos', [ 'id' => $motivos_id ] );
        }

        $motivos = $motivos->get();
        // dd($motivos);
        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'motivos',
            'motivos'=> $motivos,
            'estados' => collect($this->getEstados())->toBase(),
            'request' => $request,
            'rotaIncluir' => 'incluir-motivos',
            'rotaAlterar' => 'alterar-motivos'
        );

        return view('motivos', $data);
    }

    public function salva($request) {


        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        if($request->input('id')) {

            $motivos = motivos::where('id', $request->input('id'))->first();
        }


        $motivos->nome = $request->input('nome');
        $motivos->ativo = $request->input('ativo');

        $motivos->save();

        return $motivos->id;
    }

    public function getAllMotivos() {
        $motivos = new Motivos();
        $query = $motivos->where('ativo', '=', 1);

        return $query->get();
    }
}
