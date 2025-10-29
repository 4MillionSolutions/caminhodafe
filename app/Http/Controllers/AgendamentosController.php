<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Agendamentos;
use App\Models\Clientes;
use App\Models\Imoveis;
use App\Models\Prestadores;
use App\Models\Motivos;
use Carbon\Carbon;

class AgendamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $agendamentos = new Agendamentos();

        if ($id) {
            $agendamentos = $agendamentos->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $agendamentos = $agendamentos->where('ativo', '=', $request->input('ativo'));
        } else {
            $agendamentos = $agendamentos->where('ativo', '=', 1);
        }

        if (!empty($request->input('numero_sequencial'))) {
            $agendamentos = $agendamentos->where('numero_sequencial', 'like', '%'.$request->input('numero_sequencial').'%');
        }

        if (!empty($request->input('cliente_id'))) {
            $agendamentos = $agendamentos->where('cliente_id', '=', $request->input('cliente_id'));
        }

        if (!empty($request->input('data'))) {
            $agendamentos = $agendamentos->where('data', '=', $request->input('data'));
        }

        $agendamentos = $agendamentos->get();
        $tela = 'pesquisa';
        $clientes = Clientes::where('ativo', true)->get();
        $prestadores = Prestadores::where('ativo', true)->get();
        
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'agendamentos',
            'agendamentos'=> $agendamentos,
            'clientes'=> $clientes,
            'prestadores'=> $prestadores,
            'request' => $request,
            'rotaIncluir' => 'incluir-agendamentos',
            'rotaAlterar' => 'alterar-agendamentos'
        );

        return view('agendamentos', $data);
    }

    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            try {
                $agendamentos_id = $this->salva($request);
                return redirect()->route('agendamentos', [ 'id' => $agendamentos_id ] );
            } catch (\Exception $e) {
                Log::error('Erro ao incluir agendamento: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Erro ao salvar agendamento: ' . $e->getMessage()]);
            }
        }

        $tela = 'incluir';
        $clientes = Clientes::where('ativo', true)->get();
        $prestadores = Prestadores::where('ativo', true)->get();
        $motivos = Motivos::where('ativo', true)->get();
        
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'agendamentos',
            'clientes' => $clientes,
            'prestadores' => $prestadores,
            'motivos' => $motivos,
            'request' => $request,
            'rotaIncluir' => 'incluir-agendamentos',
            'rotaAlterar' => 'alterar-agendamentos'
        );

        return view('agendamentos', $data);
    }

    public function alterar(Request $request)
    {
        $agendamentos = new Agendamentos();
        $agendamentos = $agendamentos->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            try {
                $agendamentos_id = $this->salva($request);
                return redirect()->route('agendamentos', [ 'id' => $agendamentos_id ] );
            } catch (\Exception $e) {
                Log::error('Erro ao alterar agendamento: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Erro ao salvar agendamento: ' . $e->getMessage()]);
            }
        }

        $agendamentos = $agendamentos->get();
        $clientes = Clientes::where('ativo', true)->get();
        $prestadores = Prestadores::where('ativo', true)->get();
        $motivos = Motivos::where('ativo', true)->get();
        
        $tela = 'alterar';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'agendamentos',
            'agendamentos'=> $agendamentos,
            'clientes' => $clientes,
            'prestadores' => $prestadores,
            'motivos' => $motivos,
            'request' => $request,
            'rotaIncluir' => 'incluir-agendamentos',
            'rotaAlterar' => 'alterar-agendamentos'
        );

        return view('agendamentos', $data);
    }

    public function salvaAgendamento(Request $request) {
        try {
            $agendamentos_id = $this->salva($request);
            return response()->json([
                'success' => true,
                'message' => 'Agendamento salvo com sucesso!',
                'data' => ['id' => $agendamentos_id]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar agendamento (wrapper)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar agendamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function salva($request) {
        try {
            $imovel_id = null;
            
            // Se os dados de imóvel vêm do formulário inline (da aba 2)
            if ($request->input('imovel_endereco')) {
                
                $imovel = new Imoveis();
                $imovel->numero = $request->input('imovel_numero') ?? '';
                $imovel->endereco = $request->input('imovel_endereco');
                $imovel->complemento = $request->input('imovel_complemento') ?? '';
                $imovel->bairro = $request->input('imovel_bairro') ?? '';
                $imovel->cidade = $request->input('imovel_cidade') ?? '';
                $imovel->estado = $request->input('imovel_uf') ?? 'SP';
                $imovel->cep = $request->input('imovel_cep') ?? '';
                $imovel->tipo = $request->input('imovel_tipo') ?? '';
                $imovel->telefone = $request->input('imovel_telefone') ?? '';
                $imovel->responsavel = $request->input('imovel_responsavel') ?? '';
                $imovel->ativo = true;
                $imovel->save();
                $imovel_id = $imovel->id;
            }

            if($request->input('id')) {
            } else {
                $agendamentos = new Agendamentos();
                $ultimoAgendamento = Agendamentos::latest('id')->first();
                $numero = ($ultimoAgendamento ? $ultimoAgendamento->id + 1 : 1);
                $agendamentos->numero_sequencial = 'OS-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
            }

            $agendamentos->cliente_id = $request->input('cliente_id');
            $agendamentos->imovel_id = $imovel_id ?? $request->input('imovel_id') ?? null;
            $agendamentos->data = $request->input('data');
            $agendamentos->hora_inicio = $request->input('hora_inicio');
            $agendamentos->hora_fim = $request->input('hora_fim') ?? null;
            $agendamentos->prestador_id = $request->input('prestador_id') ?? null;
            $agendamentos->tipo_demanda = $request->input('tipo_demanda') ?? null;
            $agendamentos->numero_proposta = $request->input('numero_proposta') ?? null;
            $agendamentos->numero_contato = $request->input('numero_contato') ?? null;
            $agendamentos->observacoes = $request->input('observacoes') ?? null;
            $agendamentos->ativo = $request->input('ativo') ? true : false;

            if ($request->hasFile('matricula')) {
                $file = $request->file('matricula');
                $filename = 'matricula_' . $agendamentos->numero_sequencial . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('agendamentos', $filename, 'public');
                $agendamentos->arquivo_matricula = $filename;
            }

            if ($request->hasFile('iptu')) {
                $file = $request->file('iptu');
                $filename = 'iptu_' . $agendamentos->numero_sequencial . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('agendamentos', $filename, 'public');
                $agendamentos->arquivo_iptu = $filename;
            }

            $agendamentos->save();

            return $agendamentos->id;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar agendamento', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    public function getImovelsByCliente($clienteId)
    {
        try {
            $imoveis = Imoveis::select('id', 'endereco')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->endereco ?? 'Imóvel sem endereço'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $imoveis
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar imóveis', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    public function getAgendamento($id)
    {
        try {
            $agendamento = Agendamentos::find($id);
            if (!$agendamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $agendamento
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar agendamento', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar agendamento'
            ], 500);
        }
    }

    public function deletaAgendamento(Request $request)
    {
        try {
            $id = $request->input('id');
            $agendamento = Agendamentos::find($id);
            
            if (!$agendamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado'
                ], 404);
            }

            $agendamento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agendamento deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar agendamento', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar agendamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getImovel($id)
    {
        try {
            $imovel = Imoveis::find($id);
            if (!$imovel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imóvel não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $imovel
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar imóvel', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar imóvel'
            ], 500);
        }
    }

    public function deletaImovel(Request $request)
    {
        try {
            $id = $request->input('id');
            $imovel = Imoveis::find($id);
            
            if (!$imovel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imóvel não encontrado'
                ], 404);
            }

            $imovel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Imóvel deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar imóvel', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar imóvel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajax()
    {
        $agendamentos = Agendamentos::with(['cliente', 'imovel', 'prestador'])
            ->get();

        $dados = [];
        foreach($agendamentos as $agendamento) {
            $dados[] = [
                'id' => $agendamento->id,
                'numero_sequencial' => $agendamento->numero_sequencial,
                'cliente' => $agendamento->cliente->nome ?? '-',
                'imovel' => $agendamento->imovel->nome ?? '-',
                'endereco' => $agendamento->imovel->endereco ?? '-',
                'data' => \Carbon\Carbon::parse($agendamento->data)->format('d/m/Y'),
                'hora_inicio' => $agendamento->hora_inicio,
                'prestador' => $agendamento->prestador->nome ?? '-',
                'ativo' => $agendamento->ativo ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-danger">Inativo</span>',
                'acoes' => '<button type="button" class="btn btn-sm btn-primary" data-action="editar" data-type="agendamento" data-id="' . $agendamento->id . '">Editar</button> <button type="button" class="btn btn-sm btn-danger" data-action="deletar" data-type="agendamento" data-id="' . $agendamento->id . '">Deletar</button>'
            ];
        }

        return response()->json([
            'data' => $dados
        ]);
    }

    public function ajaxImoveis(Request $request)
    {
        try {
            $imoveis = Imoveis::get();

            $dados = [];
            foreach($imoveis as $imovel) {
                $dados[] = [
                    'id' => $imovel->id,
                    'endereco' => $imovel->endereco ?? '-',
                    'complemento' => $imovel->complemento ?? '-',
                    'bairro' => $imovel->bairro ?? '-',
                    'cidade' => $imovel->cidade ?? '-',
                    'uf' => $imovel->estado ?? $imovel->uf ?? '-',
                    'tipo' => $imovel->tipo ?? '-',
                    'telefone' => $imovel->telefone ?? '-',
                    'contato' => $imovel->responsavel ?? $imovel->contato ?? '-',
                    'acoes' => '<button type="button" class="btn btn-sm btn-primary" data-action="editar" data-id="' . $imovel->id . '">Editar</button> <button type="button" class="btn btn-sm btn-danger" data-action="deletar" data-id="' . $imovel->id . '">Deletar</button>'
                ];
            }

            return response()->json([
                'data' => $dados
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar imóveis', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function salvaImovel(Request $request)
    {
        try {
            // Validação
            $validated = $request->validate([
                'endereco' => 'required|string|max:180',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:100',
                'uf' => 'nullable|string|max:2',
                'tipo' => 'nullable|string|max:50',
                'telefone' => 'nullable|string|max:11',
                'responsavel' => 'nullable|string|max:100',
            ]);

            if ($request->input('id')) {
                $imovel = Imoveis::find($request->input('id'));
                if (!$imovel) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Imóvel não encontrado'
                    ], 404);
                }
            } else {
                $imovel = new Imoveis();
            }

            $imovel->numero = $request->input('numero') ?? '';
            $imovel->endereco = $request->input('endereco');
            $imovel->complemento = $request->input('complemento') ?? '';
            $imovel->bairro = $request->input('bairro') ?? '';
            $imovel->cidade = $request->input('cidade') ?? '';
            $imovel->estado = $request->input('uf') ?? 'SP';
            $imovel->cep = $request->input('cep') ?? '';
            $imovel->tipo = $request->input('tipo') ?? '';
            $imovel->telefone = $request->input('telefone') ?? '';
            $imovel->responsavel = $request->input('responsavel') ?? '';
            $imovel->ativo = true;
            $imovel->save();

            return response()->json([
                'success' => true,
                'message' => 'Imóvel salvo com sucesso!',
                'data' => $imovel
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar imóvel', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar imóvel: ' . $e->getMessage()
            ], 500);
        }
    }
}
