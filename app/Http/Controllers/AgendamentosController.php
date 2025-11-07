<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Agendamentos;
use App\Models\Clientes;
use App\Models\Imoveis;
use App\Models\Prestadores;
use App\Models\Motivos;
use App\Models\Servicos;
use App\Models\AgendamentoAuditoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

    /**
     * FASE 1: NOVOS MÉTODOS PARA REDESENHO COMPLETO
     */

    /**
     * Consulta ViaCEP e auto-preenche dados do imóvel
     */
    public function consultarViaCep(Request $request)
    {
        try {
            $cep = preg_replace('/\D/', '', $request->input('cep'));
            
            if (strlen($cep) !== 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP inválido'
                ], 422);
            }

            $url = "https://viacep.com.br/ws/{$cep}/json/";
            $response = @file_get_contents($url);
            
            if ($response === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao consultar ViaCEP'
                ], 500);
            }

            $dados = json_decode($response, true);

            if (isset($dados['erro'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'logradouro' => $dados['logradouro'] ?? '',
                    'complemento' => $dados['complemento'] ?? '',
                    'bairro' => $dados['bairro'] ?? '',
                    'cidade' => $dados['localidade'] ?? '',
                    'estado' => $dados['uf'] ?? '',
                    'cep' => $cep
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar ViaCEP', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar ViaCEP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salva novo imóvel com dados do ViaCEP e retorna para seleção de prestadores
     */
    public function cadastrarImovel(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'cep' => 'required|string',
                'numero' => 'required|string|max:20',
                'complemento' => 'nullable|string|max:200',
                'tipo_enum' => 'required|in:CS,AP,LT,GLP,PD,LJ,SL,OUTROS'
            ]);

            $imovel = new Imoveis();
            $imovel->cliente_id = $validated['cliente_id'];
            $imovel->cep = preg_replace('/\D/', '', $validated['cep']);
            $imovel->numero = $validated['numero'];
            
            // Consulta ViaCEP e auto-preenche
            $cepData = $this->obterDadosViaCep($imovel->cep);
            if ($cepData) {
                $imovel->logradouro = $cepData['logradouro'];
                $imovel->complemento_viacep = $cepData['complemento'];
                $imovel->bairro = $cepData['bairro'];
                $imovel->cidade = $cepData['localidade'];
                $imovel->estado = $cepData['uf'];
            }

            $imovel->tipo_enum = $validated['tipo_enum'];
            $imovel->usuario_criacao_id = Auth::id();
            $imovel->data_criacao = now();
            $imovel->save();

            // Registra auditoria
            $this->registrarAuditoriaImovel($imovel->id, 'CRIADO');

            return response()->json([
                'success' => true,
                'message' => 'Imóvel cadastrado com sucesso!',
                'data' => $imovel
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar imóvel', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar imóvel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna prestadores recomendados para um imóvel (smart selection)
     */
    public function getPrestatoresRecomendados(Request $request)
    {
        try {
            $imovelId = $request->input('imovel_id');
            $imovel = Imoveis::find($imovelId);

            if (!$imovel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imóvel não encontrado'
                ], 404);
            }

            // Filtro de localização (estado, cidade, bairro)
            $prestadores = Prestadores::byLocalizacaoAtendimento(
                $imovel->estado,
                $imovel->cidade,
                $imovel->bairro
            )
            ->ativos()
            ->ordenadoAvaliacao()
            ->get()
            ->map(function ($prestador) {
                return [
                    'id' => $prestador->id,
                    'nome' => $prestador->nome_completo,
                    'localizacao' => $prestador->localizacao_atendimento,
                    'valor_hora' => $prestador->valor_hora ?? 'N/A',
                    'avaliacao' => $prestador->avaliacao ?? '0',
                    'telefone' => $prestador->telefone_comercial ?? $prestador->telefone,
                    'whatsapp' => $prestador->whatsapp ?? null
                ];
            });

            // Se não encontrar nenhum na localidade exata, busca apenas por estado
            if ($prestadores->isEmpty()) {
                $prestadores = Prestadores::where('estado_atendimento', $imovel->estado)
                    ->ativos()
                    ->ordenadoAvaliacao()
                    ->get()
                    ->map(function ($prestador) {
                        return [
                            'id' => $prestador->id,
                            'nome' => $prestador->nome_completo,
                            'localizacao' => $prestador->localizacao_atendimento,
                            'valor_hora' => $prestador->valor_hora ?? 'N/A',
                            'avaliacao' => $prestador->avaliacao ?? '0',
                            'telefone' => $prestador->telefone_comercial ?? $prestador->telefone,
                            'whatsapp' => $prestador->whatsapp ?? null
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'data' => $prestadores->values()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar prestadores recomendados', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar prestadores'
            ], 500);
        }
    }

    /**
     * Atribui agendamento a um prestador e muda status para ATRIBUIDO
     */
    public function atribuirAoPrestador(Request $request)
    {
        try {
            $validated = $request->validate([
                'agendamento_id' => 'required|exists:agendamentos,id',
                'prestador_id' => 'required|exists:prestadores,id'
            ]);

            $agendamento = Agendamentos::find($validated['agendamento_id']);
            
            $valorAnterior = [
                'prestador_id' => $agendamento->prestador_id,
                'status' => $agendamento->status
            ];

            $agendamento->prestador_id = $validated['prestador_id'];
            $agendamento->status = 'ATRIBUIDO';
            $agendamento->data_atribuicao = now();
            $agendamento->usuario_atribuicao_id = Auth::id();
            $agendamento->save();

            // Registra auditoria
            $agendamento->registrarAuditoria(
                'ATRIBUIDO',
                Auth::id(),
                'prestador_id',
                $valorAnterior['prestador_id'],
                $validated['prestador_id']
            );

            $agendamento->registrarAuditoria(
                'STATUS_ALTERADO',
                Auth::id(),
                'status',
                'RASCUNHO',
                'ATRIBUIDO'
            );

            return response()->json([
                'success' => true,
                'message' => 'Agendamento atribuído com sucesso!',
                'data' => $agendamento
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atribuir agendamento', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atribuir agendamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envia agendamento para PRODUCAO
     */
    public function enviarParaProducao(Request $request)
    {
        try {
            $validated = $request->validate([
                'agendamento_id' => 'required|exists:agendamentos,id'
            ]);

            $agendamento = Agendamentos::find($validated['agendamento_id']);

            if ($agendamento->status !== 'ATRIBUIDO') {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas agendamentos ATRIBUIDOS podem ser enviados para PRODUCAO'
                ], 422);
            }

            $statusAnterior = $agendamento->status;
            $agendamento->status = 'PRODUCAO';
            $agendamento->data_producao = now();
            $agendamento->usuario_producao_id = Auth::id();
            $agendamento->save();

            // Registra auditoria
            $agendamento->registrarAuditoria(
                'ENVIADO_PRODUCAO',
                Auth::id(),
                'status',
                $statusAnterior,
                'PRODUCAO'
            );

            return response()->json([
                'success' => true,
                'message' => 'Agendamento enviado para produção!',
                'data' => $agendamento
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar para produção', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar para produção: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reagenda um agendamento para nova data/hora
     */
    public function reagendar(Request $request)
    {
        try {
            $validated = $request->validate([
                'agendamento_id' => 'required|exists:agendamentos,id',
                'data_agendamento' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'observacao' => 'nullable|string'
            ]);

            $agendamento = Agendamentos::find($validated['agendamento_id']);
            
            $dadosAntigos = [
                'data' => $agendamento->data,
                'hora_inicio' => $agendamento->hora_inicio
            ];

            $agendamento->data = $validated['data_agendamento'];
            $agendamento->hora_inicio = $validated['hora_inicio'];
            $agendamento->status = 'REAGENDADO';
            $agendamento->save();

            // Registra auditoria
            $agendamento->registrarAuditoria(
                'REAGENDADO',
                Auth::id(),
                'data_hora',
                $dadosAntigos['data'] . ' ' . $dadosAntigos['hora_inicio'],
                $validated['data_agendamento'] . ' ' . $validated['hora_inicio']
            );

            if ($validated['observacao']) {
                $agendamento->registrarAuditoria(
                    'OBSERVACAO_ADICIONAL',
                    Auth::id(),
                    'observacao',
                    null,
                    $validated['observacao']
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Agendamento reagendado com sucesso!',
                'data' => $agendamento
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao reagendar', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao reagendar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria agendamento RETORNO (cópia do original com OS prefixada com R-)
     */
    public function retorno(Request $request)
    {
        try {
            $validated = $request->validate([
                'agendamento_referencia_id' => 'required|exists:agendamentos,id',
                'motivo_retorno' => 'required|string|max:500',
                'data_agendamento' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i'
            ]);

            $agendamentoOrigem = Agendamentos::find($validated['agendamento_referencia_id']);

            // Gera nova OS com prefixo R
            $novaOs = $this->gerarOsRetorno($agendamentoOrigem->os_interna);

            $agendamentoRetorno = new Agendamentos();
            $agendamentoRetorno->cliente_id = $agendamentoOrigem->cliente_id;
            $agendamentoRetorno->imovel_id = $agendamentoOrigem->imovel_id;
            $agendamentoRetorno->os_interna = $novaOs;
            $agendamentoRetorno->os_plataforma = $agendamentoOrigem->os_plataforma;
            $agendamentoRetorno->data = $validated['data_agendamento'];
            $agendamentoRetorno->hora_inicio = $validated['hora_inicio'];
            $agendamentoRetorno->contato_nome = $agendamentoOrigem->contato_nome;
            $agendamentoRetorno->numero_contato_formatted = $agendamentoOrigem->numero_contato_formatted;
            $agendamentoRetorno->observacao_externa = $validated['motivo_retorno'];
            $agendamentoRetorno->agendamento_referencia_id = $validated['agendamento_referencia_id'];
            $agendamentoRetorno->tipo_demanda_enum = 'RETORNO';
            $agendamentoRetorno->status = 'RASCUNHO';
            $agendamentoRetorno->usuario_criacao_id = Auth::id();
            $agendamentoRetorno->data_criacao = now();
            $agendamentoRetorno->save();

            // Registra auditoria
            $agendamentoRetorno->registrarAuditoria(
                'CRIADO_RETORNO',
                Auth::id(),
                'agendamento_referencia_id',
                null,
                $validated['agendamento_referencia_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Retorno criado com sucesso!',
                'data' => $agendamentoRetorno
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao criar retorno', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar retorno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria agendamento REAVALIACAO (cópia do original com tipo REAVALIACAO)
     */
    public function reavaliacao(Request $request)
    {
        try {
            $validated = $request->validate([
                'agendamento_referencia_id' => 'required|exists:agendamentos,id',
                'motivo_reavaliacao' => 'required|string|max:500',
                'data_agendamento' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i'
            ]);

            $agendamentoOrigem = Agendamentos::find($validated['agendamento_referencia_id']);

            $agendamentoReavaliacao = new Agendamentos();
            $agendamentoReavaliacao->cliente_id = $agendamentoOrigem->cliente_id;
            $agendamentoReavaliacao->imovel_id = $agendamentoOrigem->imovel_id;
            $agendamentoReavaliacao->os_interna = $agendamentoOrigem->os_interna;
            $agendamentoReavaliacao->os_plataforma = $agendamentoOrigem->os_plataforma;
            $agendamentoReavaliacao->data = $validated['data_agendamento'];
            $agendamentoReavaliacao->hora_inicio = $validated['hora_inicio'];
            $agendamentoReavaliacao->contato_nome = $agendamentoOrigem->contato_nome;
            $agendamentoReavaliacao->numero_contato_formatted = $agendamentoOrigem->numero_contato_formatted;
            $agendamentoReavaliacao->observacao_externa = $validated['motivo_reavaliacao'];
            $agendamentoReavaliacao->agendamento_referencia_id = $validated['agendamento_referencia_id'];
            $agendamentoReavaliacao->tipo_demanda_enum = 'REAVALIACAO';
            $agendamentoReavaliacao->status = 'RASCUNHO';
            $agendamentoReavaliacao->usuario_criacao_id = Auth::id();
            $agendamentoReavaliacao->data_criacao = now();
            $agendamentoReavaliacao->save();

            // Registra auditoria
            $agendamentoReavaliacao->registrarAuditoria(
                'CRIADO_REAVALIACAO',
                Auth::id(),
                'agendamento_referencia_id',
                null,
                $validated['agendamento_referencia_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Reavaliação criada com sucesso!',
                'data' => $agendamentoReavaliacao
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validação falhou',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao criar reavaliação', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar reavaliação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém histórico de auditoria (rastreamento) de um agendamento
     */
    public function getAuditoriaAgendamento(Request $request)
    {
        try {
            $agendamentoId = $request->input('agendamento_id');
            
            $auditoria = AgendamentoAuditoria::byAgendamento($agendamentoId)
                ->with('usuario')
                ->get()
                ->map(function ($registro) {
                    return [
                        'id' => $registro->id,
                        'acao' => $registro->acao,
                        'campo_alterado' => $registro->campo_alterado,
                        'valor_anterior' => $registro->valor_anterior,
                        'valor_novo' => $registro->valor_novo,
                        'usuario' => $registro->usuario->name ?? 'Sistema',
                        'data_acao' => $registro->data_acao->format('d/m/Y H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $auditoria
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar auditoria', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar auditoria'
            ], 500);
        }
    }

    /**
     * MÉTODOS AUXILIARES PRIVADOS
     */

    /**
     * Consulta ViaCEP e retorna dados
     */
    private function obterDadosViaCep($cep)
    {
        try {
            $cep = preg_replace('/\D/', '', $cep);
            if (strlen($cep) !== 8) {
                return null;
            }

            $url = "https://viacep.com.br/ws/{$cep}/json/";
            $response = @file_get_contents($url);
            
            if ($response === false) {
                return null;
            }

            $dados = json_decode($response, true);
            
            if (isset($dados['erro'])) {
                return null;
            }

            return $dados;
        } catch (\Exception $e) {
            Log::error('Erro ao consultar ViaCEP', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Gera OS com prefixo R para retornos
     */
    private function gerarOsRetorno($osOrigem)
    {
        // Se a OS original é OS-00001, gera R-OS-00001
        // Se já é um retorno R-OS-00001, gera R2-OS-00001
        
        if (strpos($osOrigem, 'R') === 0) {
            // Já é um retorno, incrementa o contador
            preg_match('/^R(\d*)-/', $osOrigem, $matches);
            $contador = isset($matches[1]) && $matches[1] ? (int)$matches[1] + 1 : 2;
            return 'R' . $contador . '-' . substr($osOrigem, strlen('R') + 1);
        }
        
        return 'R-' . $osOrigem;
    }

    /**
     * Registra auditoria de imóvel
     */
    private function registrarAuditoriaImovel($imovelId, $acao)
    {
        // Pode ser expandido para logs mais detalhados conforme necessário
        Log::info("Auditoria Imóvel", [
            'imovel_id' => $imovelId,
            'acao' => $acao,
            'usuario_id' => Auth::id(),
            'timestamp' => now()
        ]);
    }
}
