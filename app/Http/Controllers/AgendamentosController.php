<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Agendamentos;
use App\Models\Clientes;
use App\Models\Imoveis;
use App\Models\Prestadores;
use App\Models\PrestadoresRegioes;
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
        $servicos = Servicos::where('ativo', true)->get();
        
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'agendamentos',
            'agendamentos'=> $agendamentos,
            'clientes'=> $clientes,
            'prestadores'=> $prestadores,
            'servicos'=> $servicos,
            'request' => $request,
            'rotaIncluir' => 'incluir-agendamentos',
            'rotaAlterar' => 'alterar-agendamentos'
        );

        return view('agendamentos_novo', $data);
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

            Log::info('DEBUG: Recebendo dados do frontend', [
                'cliente_id' => $request->input('cliente_id'),
                'imovel_id' => $request->input('imovel_id'),
                'prestador_id' => $request->input('prestador_id'),
                'todos_inputs' => $request->all()
            ]);

            $agendamentos->cliente_id = $request->input('cliente_id');
            $agendamentos->imovel_id = $imovel_id ?? $request->input('imovel_id') ?? null;
            $agendamentos->data = $request->input('data');
            $agendamentos->hora_inicio = $request->input('hora_inicio');
            $agendamentos->hora_fim = $request->input('hora_fim') ?? null;
            $agendamentos->prestador_id = $request->input('prestador_id') ?? null;
            $agendamentos->servico_id = $request->input('servico_id') ?? null;
            $agendamentos->numero_contato = $request->input('numero_contato_formatted') ?? $request->input('numero_contato') ?? null;
            $agendamentos->contato_nome = $request->input('contato_nome') ?? null;
            $agendamentos->observacao_externa = $request->input('observacao_externa') ?? null;
            $agendamentos->data_criacao_demanda = $request->input('data_criacao_demanda') ?? now();
            $agendamentos->data_vencimento_sla = $request->input('data_vencimento_sla') ?? null;
            $agendamentos->os_plataforma = $request->input('os_plataforma') ?? null;
            $agendamentos->ativo = $request->input('ativo') ? true : false;
            $agendamentos->usuario_criacao_id = auth()->id() ?? 1;

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

            Log::info('Agendamento salvo com sucesso', [
                'id' => $agendamentos->id,
                'cliente_id' => $agendamentos->cliente_id,
                'prestador_id' => $agendamentos->prestador_id,
                'imovel_id' => $agendamentos->imovel_id
            ]);

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
        $agendamentos = Agendamentos::with(['cliente', 'imovel', 'prestador', 'servico'])
            ->orderBy('id', 'desc')
            ->get();

        $dados = [];
        foreach($agendamentos as $agendamento) {
            $cliente = $agendamento->cliente;
            $cliente_nome = ($cliente ? ($cliente->nome ?? $cliente->nome_empresa ?? '-') : '-');
            $imovel_endereco = optional($agendamento->imovel)->endereco ?? '-';
            $prestador_nome = optional($agendamento->prestador)->nome ?? '-';
            $servico_nome = optional($agendamento->servico)->nome ?? '-';
            $deslocamento_valor = $agendamento->deslocamento_valor ?? 0;
            
            $deslocamento_badge = '';
            if ($deslocamento_valor > 0) {
                $deslocamento_badge = '<span class="badge badge-info">R$ ' . number_format($deslocamento_valor, 2, ',', '.') . '</span>';
            }
            
            $dados[] = [
                'id' => $agendamento->id,
                'numero_sequencial' => $agendamento->numero_sequencial,
                'cliente' => $cliente_nome,
                'imovel' => $imovel_endereco,
                'endereco' => $imovel_endereco,
                'data' => $agendamento->data_criacao_demanda ? \Carbon\Carbon::parse($agendamento->data_criacao_demanda)->format('d/m/Y') : '-',
                'hora_inicio' => $agendamento->data_criacao_demanda ? '08:00' : '-',
                'servico' => $servico_nome,
                'prestador' => $prestador_nome,
                'deslocamento' => $deslocamento_badge ?: '-',
                'ativo' => $agendamento->status == 'CRIADO' ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">' . ($agendamento->status ?? 'Indefinido') . '</span>',
                'acoes' => '<button type="button" class="btn btn-sm btn-primary" onclick="editarAgendamento(' . $agendamento->id . ')"><i class="fas fa-edit"></i> Editar</button> <button type="button" class="btn btn-sm btn-danger" onclick="deletarAgendamento(' . $agendamento->id . ')"><i class="fas fa-trash"></i> Deletar</button>'
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
            Log::info('DEBUG cadastrarImovel: Dados recebidos', $request->all());
            
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'cep' => 'required|string',
                'numero' => 'required|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'complemento' => 'nullable|string|max:200',
                'bairro' => 'nullable|string|max:100',
                'cidade' => 'nullable|string|max:100',
                'estado' => 'nullable|string|max:2',
                'tipo_enum' => 'required|in:CS,AP,LT,GLP,PD,LJ,SL,OUTROS'
            ]);

            $imovel = new Imoveis();
            $imovel->cep = preg_replace('/\D/', '', $validated['cep']);
            $imovel->numero = $validated['numero'];
            $imovel->endereco = $validated['endereco'] ?? '';
            $imovel->complemento = $validated['complemento'] ?? '';
            $imovel->bairro = $validated['bairro'] ?? '';
            $imovel->cidade = $validated['cidade'] ?? '';
            $imovel->estado = $validated['estado'] ?? 'SP';
            $imovel->tipo = 'outro';
            $imovel->responsavel = '';
            $imovel->telefone = '';
            
            // Consulta ViaCEP e auto-preenche se não houver dados
            if (empty($imovel->endereco)) {
                $cepData = $this->obterDadosViaCep($imovel->cep);
                if ($cepData) {
                    $imovel->endereco = $cepData['logradouro'] ?? '';
                    $imovel->complemento_viacep = $cepData['complemento'] ?? '';
                    $imovel->bairro = $cepData['bairro'] ?? $imovel->bairro;
                    $imovel->cidade = $cepData['localidade'] ?? $imovel->cidade;
                    $imovel->estado = $cepData['uf'] ?? $imovel->estado;
                }
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
            $cep = $request->input('cep');
            $estado = $request->input('estado');
            $cidade = $request->input('cidade');
            
            Log::info('Buscando prestadores', ['cep' => $cep, 'estado' => $estado, 'cidade' => $cidade]);
            
            if (!$estado || !$cidade) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado e cidade são obrigatórios'
                ], 400);
            }

            // Buscar prestadores da região (estado + cidade)
            try {
                $prestadores = Prestadores::where('estado_atendimento', $estado)
                    ->where('cidade_atendimento', $cidade)
                    ->get()
                    ->map(function ($prestador) {
                        return [
                            'id' => $prestador->id,
                            'nome' => $prestador->nome,
                            'localizacao' => $prestador->cidade_atendimento . ', ' . $prestador->estado_atendimento,
                            'valor_hora' => $prestador->valor_hora ?? 'N/A',
                            'avaliacao' => $prestador->avaliacao ?? '0',
                            'telefone' => $prestador->telefone_comercial ?? $prestador->telefone ?? 'Sem telefone',
                            'whatsapp' => $prestador->whatsapp ?? null,
                        ];
                    });

                if ($prestadores->isEmpty()) {
                    // Se não encontrar na região, retornar todos os prestadores
                    $prestadores = Prestadores::all()
                        ->map(function ($prestador) {
                            return [
                                'id' => $prestador->id,
                                'nome' => $prestador->nome,
                                'localizacao' => $prestador->cidade_atendimento . ', ' . $prestador->estado_atendimento,
                                'valor_hora' => $prestador->valor_hora ?? 'N/A',
                                'avaliacao' => $prestador->avaliacao ?? '0',
                                'telefone' => $prestador->telefone_comercial ?? $prestador->telefone ?? 'Sem telefone',
                                'whatsapp' => $prestador->whatsapp ?? null,
                            ];
                        });
                }

                return response()->json([
                    'success' => true,
                    'data' => $prestadores
                ]);
            } catch (\Exception $e) {
                Log::error('Erro ao buscar modelo Prestadores', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Fallback: retornar array vazio
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao buscar prestadores recomendados', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar prestadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtém latitude e longitude de um CEP via ViaCEP
     */
    private function obterLatLngDoCep($cep)
    {
        try {
            $url = "https://viacep.com.br/ws/$cep/json/";
            $response = file_get_contents($url);
            
            if (!$response) {
                return null;
            }
            
            $dados = json_decode($response, true);
            
            if (isset($dados['erro'])) {
                return null;
            }
            
            // ViaCEP não retorna lat/lng diretamente, precisamos fazer geocoding reverso
            // Por enquanto, retornar null e usar filtro por estado/cidade
            return null;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Erro ao obter lat/lng do CEP', [
                'cep' => $cep,
                'message' => $e->getMessage()
            ]);
            return null;
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

    /**
     * Carrega dados de um agendamento para edição
     */
    public function editar($id)
    {
        Log::info('AgendamentosController@editar chamado', ['id' => $id, 'user_id' => Auth::id()]);
        try {
            $agendamento = Agendamentos::with(['cliente', 'imovel', 'prestador', 'servico'])->find($id);
            
            if (!$agendamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $agendamento->id,
                    'numero_sequencial' => $agendamento->numero_sequencial ?? $agendamento->os_interna,
                    'cliente_nome' => $agendamento->cliente->nome ?? $agendamento->cliente->nome_empresa ?? '-',
                    'cliente_id' => $agendamento->cliente_id,
                    'imovel_endereco' => $agendamento->imovel->endereco ?? '-',
                    'imovel_complemento' => $agendamento->imovel->complemento ?? '',
                    'prestador_nome' => $agendamento->prestador->nome ?? '-',
                    'servico_id' => $agendamento->servico_id ?? '',
                    'data' => $agendamento->data_criacao_demanda ? \Carbon\Carbon::parse($agendamento->data_criacao_demanda)->format('Y-m-d') : date('Y-m-d'),
                    'data_vencimento_sla' => $agendamento->data_vencimento_sla ? \Carbon\Carbon::parse($agendamento->data_vencimento_sla)->format('Y-m-d\TH:i') : '',
                    'hora_inicio' => $agendamento->hora_inicio ?? '08:00',
                    'hora_fim' => $agendamento->hora_fim ?? '17:00',
                    'contato_nome' => $agendamento->contato_nome ?? '',
                    'telefone_contato' => $agendamento->numero_contato_formatted ?? '',
                    'deslocamento_valor' => $agendamento->deslocamento_valor ?? 0,
                    'deslocamento_observacoes' => $agendamento->deslocamento_observacoes ?? '',
                    'status' => $agendamento->status ?? 'CRIADO',
                    'ativo' => 1
                ]
            ]);
        } catch(\Exception $e) {
            Log::error('Erro ao carregar agendamento para edição', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar agendamento'
            ], 500);
        }
    }

    /**
     * Atualiza dados de um agendamento
     */
    public function atualizar(Request $request, $id)
    {
        Log::info('AgendamentosController@atualizar chamado', ['id' => $id, 'user_id' => Auth::id(), 'input' => $request->all()]);
        try {
            $agendamento = Agendamentos::find($id);
            
            if (!$agendamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado'
                ], 404);
            }

            // Armazenar valores antigos para auditoria
            $valoresAntigos = [
                'cliente_id' => $agendamento->cliente_id,
                'contato_nome' => $agendamento->contato_nome,
                'numero_contato_formatted' => $agendamento->numero_contato_formatted,
                'data_criacao_demanda' => $agendamento->data_criacao_demanda,
                'data_vencimento_sla' => $agendamento->data_vencimento_sla,
                'deslocamento_valor' => $agendamento->deslocamento_valor,
                'deslocamento_observacoes' => $agendamento->deslocamento_observacoes,
                'servico_id' => $agendamento->servico_id,
                'status' => $agendamento->status
            ];

            // Atualizar campos
            if ($request->has('cliente_id')) {
                $agendamento->cliente_id = $request->input('cliente_id');
            }
            if ($request->has('contato_nome')) {
                $agendamento->contato_nome = $request->input('contato_nome');
            }
            if ($request->has('telefone_contato')) {
                $agendamento->numero_contato_formatted = $request->input('telefone_contato');
            }
            if ($request->has('data')) {
                $agendamento->data_criacao_demanda = $request->input('data');
            }
            if ($request->has('data_vencimento_sla')) {
                $agendamento->data_vencimento_sla = $request->input('data_vencimento_sla');
            }
            if ($request->has('hora_inicio')) {
                $agendamento->hora_inicio = $request->input('hora_inicio');
            }
            if ($request->has('hora_fim')) {
                $agendamento->hora_fim = $request->input('hora_fim');
            }
            if ($request->has('deslocamento_valor')) {
                $agendamento->deslocamento_valor = $request->input('deslocamento_valor');
            }
            if ($request->has('deslocamento_observacoes')) {
                $agendamento->deslocamento_observacoes = $request->input('deslocamento_observacoes');
            }
            if ($request->has('servico_id')) {
                $agendamento->servico_id = $request->input('servico_id');
            }
            if ($request->has('status')) {
                $agendamento->status = $request->input('status');
            }
            
            // Atualizar complemento do imóvel se necessário
            if ($request->has('imovel_complemento') && $agendamento->imovel_id) {
                $agendamento->imovel->complemento = $request->input('imovel_complemento');
                $agendamento->imovel->save();
            }
            
            $agendamento->save();

            // Registrar auditoria
            $mudancas = [];
            foreach($valoresAntigos as $campo => $valorAntigo) {
                $valorNovo = $agendamento->$campo;
                if ($valorAntigo != $valorNovo) {
                    $mudancas[] = [
                        'campo' => $campo,
                        'valor_antigo' => $valorAntigo,
                        'valor_novo' => $valorNovo
                    ];
                }
            }

            if (!empty($mudancas)) {
                AgendamentoAuditoria::create([
                    'agendamento_id' => $agendamento->id,
                    'usuario_id' => Auth::id(),
                    'acao' => 'atualizar',
                    'descricao' => 'Agendamento atualizado',
                    'dados_anteriores' => json_encode($valoresAntigos),
                    'dados_novos' => json_encode([
                        'cliente_id' => $agendamento->cliente_id,
                        'contato_nome' => $agendamento->contato_nome,
                        'numero_contato_formatted' => $agendamento->numero_contato_formatted,
                        'data_criacao_demanda' => $agendamento->data_criacao_demanda,
                        'data_vencimento_sla' => $agendamento->data_vencimento_sla,
                        'deslocamento_valor' => $agendamento->deslocamento_valor,
                        'deslocamento_observacoes' => $agendamento->deslocamento_observacoes,
                        'servico_id' => $agendamento->servico_id,
                        'status' => $agendamento->status
                    ])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Agendamento atualizado com sucesso!',
                'data' => $agendamento
            ]);
        } catch(\Exception $e) {
            Log::error('Erro ao atualizar agendamento', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar agendamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deleta um agendamento
     */
    public function deletar(Request $request, $id)
    {
        Log::info('AgendamentosController@deletar chamado', ['id' => $id, 'user_id' => Auth::id(), 'input' => $request->all()]);
        try {
            $agendamento = Agendamentos::find($id);
            
            if (!$agendamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamento não encontrado'
                ], 404);
            }

            $numero_sequencial = $agendamento->numero_sequencial ?? $agendamento->os_interna;

            // Registrar auditoria antes de deletar
            AgendamentoAuditoria::create([
                'agendamento_id' => $agendamento->id,
                'usuario_id' => Auth::id(),
                'acao' => 'deletar',
                'descricao' => 'Agendamento deletado',
                'dados_anteriores' => json_encode($agendamento->toArray()),
                'dados_novos' => null
            ]);

            // Deletar agendamento
            $agendamento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agendamento ' . $numero_sequencial . ' deletado com sucesso!'
            ]);
        } catch(\Exception $e) {
            Log::error('Erro ao deletar agendamento', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar agendamento: ' . $e->getMessage()
            ], 500);
        }
    }
}
