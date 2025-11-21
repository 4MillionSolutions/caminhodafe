<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Rules\ValidCPF;

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

        if (!empty($request->input('documento'))) {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
            $clientes = $clientes->where('documento', '=', $documento);
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
            'estados' => $this->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-clientes',
            'rotaAlterar' => 'alterar-clientes'
        );

        return view('clientes', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $validated = $request->validate([
                'nome' => 'required|string|max:200',
                'documento' => ['required', new ValidCPF],
                'telefone' => 'nullable|string|max:11',
                'email' => 'nullable|email|max:200',
                'data_nascimento' => 'required|date|before:today|after:1900-01-01',
                'endereco' => 'nullable|string|max:500',
                'numero' => 'nullable|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:150',
                'estado' => 'nullable|integer',
                'cep' => 'nullable|string|max:8',
                'ativo' => 'required|boolean',
            ]);

            $paciente_id = $this->salva($request);
            return redirect()->route('clientes', [ 'id' => $paciente_id ] )
                ->with('success', 'Cliente cadastrado com sucesso!');
        }

        $tela = 'incluir';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'clientes',
            'estados' => $this->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-clientes',
            'rotaAlterar' => 'alterar-clientes'
        );

        return view('clientes', $data);
    }

    public function alterar(Request $request)
    {
        $clientes = new Clientes();
        $paciente = $clientes->where('id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $validated = $request->validate([
                'nome' => 'required|string|max:200',
                'documento' => ['required', new ValidCPF],
                'telefone' => 'nullable|string|max:11',
                'email' => 'nullable|email|max:200',
                'data_nascimento' => 'required|date|before:today|after:1900-01-01',
                'endereco' => 'nullable|string|max:500',
                'numero' => 'nullable|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:150',
                'estado' => 'nullable|integer',
                'cep' => 'nullable|string|max:8',
                'ativo' => 'required|boolean',
            ]);

            $paciente_id = $this->salva($request);
            return redirect()->route('clientes', [ 'id' => $paciente_id ] )
                ->with('success', 'Cliente atualizado com sucesso!');
        }

        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'clientes',
            'clientes'=> $paciente,
            'estados' => $this->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-clientes',
            'rotaAlterar' => 'alterar-clientes'
        );

        return view('clientes', $data);
    }

    public function salva($request) {

        $clientes = new Clientes();
        if($request->input('id')) {
            $clientes = $clientes::find($request->input('id'));
        }

        $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        $clientes->nome = $request->input('nome');
        $clientes->documento = $documento;
        $clientes->data_nascimento = $request->input('data_nascimento');
        $clientes->observacoes = $request->input('observacoes');
        $clientes->numero = $request->input('numero');
        $clientes->complemento = $request->input('complemento');
        $clientes->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
        $clientes->cep = preg_replace("/[^0-9]/", "", $request->input('cep'));
        $clientes->endereco = $request->input('endereco');
        $clientes->bairro = $request->input('bairro');
        $clientes->cidade = $request->input('cidade');
        $clientes->estado = $request->input('estado');
        $clientes->email = $request->input('email');
        $clientes->ativo = $request->input('ativo');

        $clientes->save();

        return $clientes->id;
    }

    public function getAllPaciente() {
        $clientes = new Clientes();
        $query = $clientes->where('status', '=', '1');

        // Filtrar por clínica selecionada na sessão
        $clinica_id = session('clinica_id');
        if ($clinica_id) {
            $query = $query->where('clinica_id', '=', $clinica_id);
        }

        return $query->get();
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
}
