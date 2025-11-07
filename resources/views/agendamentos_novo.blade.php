@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')
    <div class="form-group row">
        <h6 class="text-dark col-sm-8 col-form-label">Agendamentos - FASE 1</h6>
        <div class="col-sm-4">
            <p>
                <button class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#div_pesquisa" aria-expanded="false" aria-controls="div_pesquisa">Pesquisar</button>
                <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#modal_novo_agendamento">Novo Agendamento</button>
            </p>
        </div>
    </div>
@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">
    <style>
        /* Status Badges */
        .badge-status {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .badge-rascunho { background-color: #6c757d; }
        .badge-atribuido { background-color: #0dcaf0; }
        .badge-producao { background-color: #fd7e14; }
        .badge-concluido { background-color: #198754; }
        .badge-reagendado { background-color: #6f42c1; }
        .badge-retorno { background-color: #dc3545; }
        .badge-reavaliacao { background-color: #e83e8c; }

        /* Tabs */
        .nav-tabs .nav-link {
            color: #495057;
            border: 1px solid transparent;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #0056b3;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-bottom-color: #fff;
            font-weight: 600;
        }

        /* ViaCEP loading */
        .loading-spinner {
            display: inline-block;
            margin-left: 10px;
            display: none;
        }
        .loading-spinner.show {
            display: inline-block;
        }

        /* Prestadores Recomendados */
        .prestador-card {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .prestador-card:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.1);
        }
        .prestador-card.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.2);
        }

        /* Timeline de Auditoria */
        .audit-timeline {
            position: relative;
            padding: 1rem;
        }
        .audit-item {
            padding: 1rem;
            border-left: 3px solid #dee2e6;
            margin-bottom: 1rem;
        }
        .audit-item.action-criado { border-left-color: #198754; }
        .audit-item.action-atribuido { border-left-color: #0dcaf0; }
        .audit-item.action-producao { border-left-color: #fd7e14; }
        .audit-item.action-concluido { border-left-color: #198754; }

        .audit-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .audit-action {
            font-weight: 600;
            color: #333;
        }
        .audit-time {
            color: #6c757d;
            font-size: 0.85rem;
        }
        .audit-user {
            color: #0056b3;
            font-size: 0.9rem;
        }
        .audit-changes {
            background-color: #f8f9fa;
            padding: 0.75rem;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }
    </style>
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
    <div class="agendamentos">
        <!-- Bloco de Pesquisa -->
        <div class="row mb-3">
            <div class="col">
                <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                    <form id="filtro" action="/agendamentos" method="get" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="numero_sequencial">Nº Sequencial</label>
                                <input type="text" id="numero_sequencial" name="numero_sequencial" class="form-control" 
                                    value="{{ $request->input('numero_sequencial') ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="cliente_id">Cliente</label>
                                <select class="form-control" id="cliente_id" name="cliente_id">
                                    <option value="">Todos</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" 
                                            {{ $request->input('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome ?? $cliente->nome_empresa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Agendamentos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Agendamentos Cadastrados</h5>
                    </div>
                    <div class="card-body">
                        <table id="table_agendamentos" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>OS</th>
                                    <th>Cliente</th>
                                    <th>Imóvel</th>
                                    <th>Endereço</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Prestador</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL: NOVO AGENDAMENTO ================= -->
    <div class="modal fade" id="modal_novo_agendamento" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Novo Agendamento - FASE 1</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- ABAS PRINCIPAIS -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab_imovel" data-toggle="tab" href="#content_imovel" role="tab">
                                <i class="fas fa-home"></i> 1. Cadastro de Imóvel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab_prestador" data-toggle="tab" href="#content_prestador" role="tab">
                                <i class="fas fa-user-tie"></i> 2. Seleção de Prestador
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab_dados" data-toggle="tab" href="#content_dados" role="tab">
                                <i class="fas fa-list"></i> 3. Dados do Agendamento
                            </a>
                        </li>
                    </ul>

                    <!-- CONTEÚDO DAS ABAS -->
                    <div class="tab-content">
                        <!-- ABA 1: CADASTRO DE IMÓVEL -->
                        <div class="tab-pane fade show active" id="content_imovel" role="tabpanel">
                            <form id="form_cadastro_imovel">
                                <input type="hidden" name="imovel_id" id="input_imovel_id">

                                <h6 class="text-info mb-3"><strong>Dados do Cliente</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="input_cliente_id">Cliente *</label>
                                        <select class="form-control" id="input_cliente_id" name="cliente_id" required>
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">
                                                    {{ $cliente->nome ?? $cliente->nome_empresa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Consulta ViaCEP</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="input_cep">CEP *</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="input_cep" name="cep" 
                                                placeholder="00000-000" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" id="btn_consultar_cep">
                                                    Consultar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="loading-spinner" id="spinner_cep">
                                            <small class="text-muted">Consultando...</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="input_logradouro">Logradouro</label>
                                        <input type="text" class="form-control" id="input_logradouro" 
                                            name="logradouro" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="input_numero">Número *</label>
                                        <input type="text" class="form-control" id="input_numero" name="numero" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="input_complemento_viacep">Complemento</label>
                                        <input type="text" class="form-control" id="input_complemento_viacep" 
                                            name="complemento_viacep" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="input_bairro">Bairro</label>
                                        <input type="text" class="form-control" id="input_bairro" 
                                            name="bairro" readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="input_cidade">Cidade</label>
                                        <input type="text" class="form-control" id="input_cidade" 
                                            name="cidade" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="input_estado">Estado</label>
                                        <input type="text" class="form-control" id="input_estado" 
                                            name="estado" readonly maxlength="2">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="input_tipo_enum">Tipo *</label>
                                        <select class="form-control" id="input_tipo_enum" name="tipo_enum" required>
                                            <option value="">Selecione</option>
                                            <option value="CS">Casa</option>
                                            <option value="AP">Apartamento</option>
                                            <option value="LT">Lote</option>
                                            <option value="GLP">Galpão</option>
                                            <option value="PD">Prédio</option>
                                            <option value="LJ">Loja</option>
                                            <option value="SL">Salão</option>
                                            <option value="OUTROS">Outros</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btn_proximo_prestador">
                                        Próximo: Selecionar Prestador <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- ABA 2: SELEÇÃO DE PRESTADOR -->
                        <div class="tab-pane fade" id="content_prestador" role="tabpanel">
                            <h6 class="text-info mb-3"><strong>Prestadores Recomendados</strong></h6>
                            <p class="text-muted">
                                <small>Baseado na localização do imóvel (estado, cidade, bairro)</small>
                            </p>

                            <div id="container_prestadores" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center text-muted">
                                    <p>Selecione um imóvel primeiro para ver prestadores recomendados</p>
                                </div>
                            </div>

                            <input type="hidden" name="prestador_id" id="input_prestador_id">

                            <div class="text-right mt-4">
                                <button type="button" class="btn btn-secondary" id="btn_voltar_imovel">
                                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                                </button>
                                <button type="button" class="btn btn-primary" id="btn_proximo_dados" disabled>
                                    Próximo: Dados do Agendamento <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ABA 3: DADOS DO AGENDAMENTO -->
                        <div class="tab-pane fade" id="content_dados" role="tabpanel">
                            <form id="form_dados_agendamento">
                                <h6 class="text-info mb-3"><strong>Informações do Contato</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="input_contato_nome">Nome do Contato *</label>
                                        <input type="text" class="form-control" id="input_contato_nome" 
                                            name="contato_nome" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input_numero_contato">Telefone/WhatsApp *</label>
                                        <input type="text" class="form-control" id="input_numero_contato" 
                                            name="numero_contato_formatted" required>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Dados do Agendamento</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="input_data_agendamento">Data *</label>
                                        <input type="date" class="form-control" id="input_data_agendamento" 
                                            name="data" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input_hora_inicio">Hora Início *</label>
                                        <input type="time" class="form-control" id="input_hora_inicio" 
                                            name="hora_inicio" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input_hora_fim">Hora Fim</label>
                                        <input type="time" class="form-control" id="input_hora_fim" name="hora_fim">
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Informações SLA</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="input_data_criacao_demanda">Data Criação Demanda</label>
                                        <input type="datetime-local" class="form-control" id="input_data_criacao_demanda" 
                                            name="data_criacao_demanda">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input_data_vencimento_sla">Data Vencimento SLA</label>
                                        <input type="datetime-local" class="form-control" id="input_data_vencimento_sla" 
                                            name="data_vencimento_sla">
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>OS e Observações</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="input_os_plataforma">OS Plataforma (Cliente)</label>
                                        <input type="text" class="form-control" id="input_os_plataforma" 
                                            name="os_plataforma">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="input_observacao_externa">Observações Externas</label>
                                        <textarea class="form-control" id="input_observacao_externa" 
                                            name="observacao_externa" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-secondary" id="btn_voltar_prestador">
                                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn_salvar_agendamento_novo">
                                        <i class="fas fa-save mr-2"></i> Salvar Agendamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL: VISUALIZAR AGENDAMENTO ================= -->
    <div class="modal fade" id="modal_visualizar_agendamento" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detalhes do Agendamento</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- ABAS: DADOS E RASTREAMENTO -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab_ver_dados" data-toggle="tab" href="#content_ver_dados" role="tab">
                                <i class="fas fa-info-circle"></i> Dados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab_rastreamento" data-toggle="tab" href="#content_rastreamento" role="tab">
                                <i class="fas fa-history"></i> Rastreamento (Auditoria)
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- ABA: DADOS -->
                        <div class="tab-pane fade show active" id="content_ver_dados" role="tabpanel">
                            <input type="hidden" id="view_agendamento_id">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h6><strong>Status:</strong> 
                                        <span id="view_status_badge" class="badge badge-status"></span>
                                    </h6>
                                </div>
                            </div>

                            <h6 class="text-info mb-2"><strong>Identificação</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>OS Interna:</strong> <span id="view_os_interna"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>OS Plataforma:</strong> <span id="view_os_plataforma"></span></p>
                                </div>
                            </div>

                            <h6 class="text-info mb-2"><strong>Cliente</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Cliente:</strong> <span id="view_cliente_nome"></span></p>
                                </div>
                            </div>

                            <h6 class="text-info mb-2"><strong>Imóvel</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Endereço:</strong> <span id="view_endereco_completo"></span></p>
                                </div>
                            </div>

                            <h6 class="text-info mb-2"><strong>Agendamento</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <p><strong>Data:</strong> <span id="view_data"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Hora Início:</strong> <span id="view_hora_inicio"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Prestador:</strong> <span id="view_prestador_nome"></span></p>
                                </div>
                            </div>

                            <h6 class="text-info mb-2"><strong>Contato</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Nome:</strong> <span id="view_contato_nome"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Telefone:</strong> <span id="view_numero_contato"></span></p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Observações:</strong></p>
                                    <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 0.25rem;">
                                        <p id="view_observacao_externa" style="white-space: pre-wrap;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA: RASTREAMENTO -->
                        <div class="tab-pane fade" id="content_rastreamento" role="tabpanel">
                            <div class="audit-timeline" id="audit_timeline">
                                <div class="text-center text-muted">
                                    <p>Carregando histórico...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-warning" id="btn_reagendar" style="display:none;">
                        <i class="fas fa-calendar-alt mr-2"></i> Reagendar
                    </button>
                    <button type="button" class="btn btn-danger" id="btn_retorno" style="display:none;">
                        <i class="fas fa-redo mr-2"></i> Retorno
                    </button>
                    <button type="button" class="btn btn-info" id="btn_reavaliacao" style="display:none;">
                        <i class="fas fa-search mr-2"></i> Reavaliação
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_atribuir_prestador" style="display:none;">
                        <i class="fas fa-user-check mr-2"></i> Atribuir Prestador
                    </button>
                    <button type="button" class="btn btn-success" id="btn_enviar_producao" style="display:none;">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar Produção
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
    <script>
        $(document).ready(function() {
            console.log('Document ready!');
            
            // Máscaras
            $('#input_cep').mask('00000-000');
            $('#input_numero_contato').mask('(00) 00000-0000');
            
            // Inicializar DataTable
            inicializarDataTable();

            // Event Listeners
            setupEventListeners();
            
            console.log('Inicialização concluída!');
        });

        function inicializarDataTable() {
            console.log('Inicializando DataTable...');
            const table = $('#table_agendamentos').DataTable({
                ajax: {
                    url: '/ajax/agendamentos',
                    type: 'GET',
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('Erro ao carregar dados AJAX:', textStatus, errorThrown);
                    }
                },
                columns: [
                    { data: 'numero_sequencial' },
                    { data: 'cliente' },
                    { data: 'imovel' },
                    { data: 'endereco' },
                    { data: 'data' },
                    { data: 'hora_inicio' },
                    { data: 'prestador' },
                    { data: 'ativo' },
                    { data: 'acoes', orderable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json'
                },
                autoWidth: false,
                pageLength: 10,
                searching: true,
                ordering: true,
                paging: true,
                initComplete: function() {
                    console.log('DataTable inicializada com sucesso!');
                }
            });
            
            // Armazenar referência global
            window.agendamentosDataTable = table;
            console.log('DataTable pronta para ser recarregada');
        }

        function setupEventListeners() {
            console.log('setupEventListeners chamado');
            
            // Quando a modal abre, limpar os formulários
            $('#modal_novo_agendamento').on('show.bs.modal', function () {
                console.log('Modal abrindo!');
                limparFormularios();
            });

            // Consultar ViaCEP
            $('#btn_consultar_cep').click(function() {
                consultarViaCep();
            });

            // Próximo: Prestador
            $('#btn_proximo_prestador').click(function() {
                console.log('Botão "Próximo: Selecionar Prestador" clicado');
                if (validarFormularioImovel()) {
                    console.log('Formulário do imóvel válido, carregando prestadores');
                    carregarPrestadoresRecomendados();
                    // A aba será ativada automaticamente após carregarPrestadoresRecomendados() receber a resposta
                } else {
                    console.log('Formulário do imóvel inválido');
                }
            });

            // Voltar de Prestador para Imóvel
            $('#btn_voltar_imovel').click(function() {
                console.log('Botão "Voltar para Imóvel" clicado');
                // Remover classes active/show de todas as abas
                $('.tab-pane').removeClass('show active');
                $('.nav-link').removeClass('active');
                
                // Adicionar classes para aba de imóvel
                $('#content_imovel').addClass('show active');
                $('#tab_imovel').addClass('active');
                
                console.log('Aba "Cadastro de Imóvel" ativada!');
            });

            // Próximo: Dados
            $('#btn_proximo_dados').click(function() {
                console.log('Botão "Próximo: Dados" clicado');
                // Remover classes active/show de todas as abas
                $('.tab-pane').removeClass('show active');
                $('.nav-link').removeClass('active');
                
                // Adicionar classes para aba de dados
                $('#content_dados').addClass('show active');
                $('#tab_dados').addClass('active');
                
                console.log('Aba "Dados do Agendamento" ativada!');
            });

            // Voltar de Dados para Prestador
            $('#btn_voltar_prestador').click(function() {
                console.log('Botão "Voltar" clicado');
                // Remover classes active/show de todas as abas
                $('.tab-pane').removeClass('show active');
                $('.nav-link').removeClass('active');
                
                // Adicionar classes para aba de prestador
                $('#content_prestador').addClass('show active');
                $('#tab_prestador').addClass('active');
                
                console.log('Aba "Seleção de Prestador" ativada!');
            });

            // Salvar agendamento
            $('#btn_salvar_agendamento_novo').click(function() {
                salvarNovoAgendamento();
            });
        }

        function consultarViaCep() {
            const cep = $('#input_cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('CEP inválido');
                return;
            }

            $('#spinner_cep').addClass('show');

            $.ajax({
                url: '/agendamentos/consultar-cep',
                type: 'POST',
                data: {
                    cep: cep,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#input_logradouro').val(response.data.logradouro);
                        $('#input_complemento_viacep').val(response.data.complemento);
                        $('#input_bairro').val(response.data.bairro);
                        $('#input_cidade').val(response.data.cidade);
                        $('#input_estado').val(response.data.estado);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Erro ao consultar CEP');
                },
                complete: function() {
                    $('#spinner_cep').removeClass('show');
                }
            });
        }

        function validarFormularioImovel() {
            const clienteId = $('#input_cliente_id').val();
            const cep = $('#input_cep').val();
            const numero = $('#input_numero').val();
            const tipoEnum = $('#input_tipo_enum').val();

            if (!clienteId || !cep || !numero || !tipoEnum) {
                alert('Preench todos os campos obrigatórios!');
                return false;
            }

            return true;
        }

        function carregarPrestadoresRecomendados() {
            console.log('Iniciando carregamento de prestadores recomendados...');
            
            // Primeiro, salva o imóvel
            $.ajax({
                url: '/agendamentos/cadastrar-imovel',
                type: 'POST',
                data: {
                    cliente_id: $('#input_cliente_id').val(),
                    cep: $('#input_cep').val(),
                    numero: $('#input_numero').val(),
                    endereco: $('#input_logradouro').val(),
                    complemento: $('#input_complemento_viacep').val(),
                    bairro: $('#input_bairro').val(),
                    cidade: $('#input_cidade').val(),
                    estado: $('#input_estado').val(),
                    tipo_enum: $('#input_tipo_enum').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Imóvel salvo:', response);
                    if (response.success) {
                        $('#input_imovel_id').val(response.data.id);
                        console.log('Chamando carregarPrestadores com ID:', response.data.id);

                        // Agora carrega os prestadores
                        carregarPrestadores(response.data.id);
                    } else {
                        alert('Erro ao cadastrar imóvel: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.log('Erro ao cadastrar imóvel:', xhr);
                    alert('Erro ao cadastrar imóvel');
                }
            });
        }

        function carregarPrestadores(imovelId) {
            const url = '/agendamentos/prestadores-recomendados?imovel_id=' + imovelId;
            console.log('Chamando URL:', url);
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log('Resposta prestadores recebida:', response);
                    if (response.success) {
                        console.log('Success = true, chamando exibirPrestadores');
                        exibirPrestadores(response.data);
                        console.log('Ativando aba prestador manualmente');
                        // Remover classes active/show de todas as abas
                        $('.tab-pane').removeClass('show active');
                        $('.nav-link').removeClass('active');
                        
                        // Adicionar classes para aba do prestador
                        $('#content_prestador').addClass('show active');
                        $('#tab_prestador').addClass('active');
                        
                        console.log('Aba prestador ativada!');
                        console.log('Classes da aba:', $('#content_prestador').attr('class'));
                    } else {
                        console.log('Erro na resposta:', response.message);
                        $('#container_prestadores').html('<div class="alert alert-warning">Nenhum prestador encontrado para esta localização</div>');
                    }
                },
                error: function(xhr) {
                    console.log('Erro AJAX:', xhr);
                    console.log('Status:', xhr.status);
                    console.log('Response:', xhr.responseText);
                    alert('Erro ao carregar prestadores: ' + xhr.status);
                }
            });
        }

        function exibirPrestadores(prestadores) {
            console.log('Exibindo prestadores:', prestadores);
            let html = '';

            if (prestadores.length === 0) {
                html = '<div class="alert alert-warning">Nenhum prestador disponível</div>';
            } else {
                prestadores.forEach(function(p) {
                    html += `
                        <div class="prestador-card" style="border: 1px solid #ddd; padding: 1rem; margin-bottom: 0.5rem; cursor: pointer;" onclick="selecionarPrestador(${p.id}, '${p.nome}')">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-2">${p.nome}</h6>
                                    <p class="mb-1"><small class="text-muted">📍 ${p.localizacao || 'Sem informação'}</small></p>
                                    <p class="mb-0"><small class="text-muted">⭐ Avaliação: ${p.avaliacao || 'N/A'}</small></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h5 class="text-success mb-2">R$ ${p.valor_hora || 'N/A'}</h5>
                                    <p class="mb-0"><small>📱 ${p.telefone || 'N/A'}</small></p>
                                    ${p.whatsapp ? `<p><small>💬 WhatsApp</small></p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            console.log('HTML a inserir:', html);
            console.log('Container antes:', $('#container_prestadores').html());
            $('#container_prestadores').html(html);
            console.log('Prestadores renderizados!');
            console.log('Container depois:', $('#container_prestadores').html());
        }

        function selecionarPrestador(prestadorId, prestadorNome) {
            console.log('🎯 Prestador selecionado:', { id: prestadorId, nome: prestadorNome });
            $('#input_prestador_id').val(prestadorId);
            console.log('Prestador ID salvo no input:', $('#input_prestador_id').val());

            // Highlight
            console.log('Removendo classe selected de todos os cards');
            $('.prestador-card').removeClass('selected');
            console.log('Adicionando classe selected ao card clicado');
            event.currentTarget.classList.add('selected');

            // Habilitar próximo
            console.log('Habilitando botão "Próximo: Dados do Agendamento"');
            $('#btn_proximo_dados').prop('disabled', false);
            console.log('Botão habilitado:', $('#btn_proximo_dados').prop('disabled'));
        }

        function salvarNovoAgendamento() {
            const clienteIdElement = $('#input_cliente_id');
            const clienteIdValue = clienteIdElement.val();
            
            console.log('🔍 DEBUG Cliente ID:');
            console.log('  - Elemento encontrado:', clienteIdElement.length > 0 ? 'SIM' : 'NÃO');
            console.log('  - Valor do campo:', clienteIdValue);
            console.log('  - Tipo:', typeof clienteIdValue);
            console.log('  - HTML do select:', clienteIdElement.html());
            
            const dados = {
                cliente_id: clienteIdValue,
                imovel_id: $('#input_imovel_id').val(),
                prestador_id: $('#input_prestador_id').val(),
                contato_nome: $('#input_contato_nome').val(),
                numero_contato_formatted: $('#input_numero_contato').val(),
                data: $('#input_data_agendamento').val(),
                hora_inicio: $('#input_hora_inicio').val(),
                hora_fim: $('#input_hora_fim').val(),
                data_criacao_demanda: $('#input_data_criacao_demanda').val(),
                data_vencimento_sla: $('#input_data_vencimento_sla').val(),
                os_plataforma: $('#input_os_plataforma').val(),
                observacao_externa: $('#input_observacao_externa').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            console.log('📤 Dados a enviar:', dados);

            $.ajax({
                url: '/agendamentos/salva',
                type: 'POST',
                data: dados,
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.success) {
                        console.log('Agendamento salvo com sucesso!');
                        alert('Agendamento salvo com sucesso!');
                        
                        // Fechar modal sem usar .modal() que não está disponível
                        // Usar trigger para simular clique no botão de fechar
                        $('#modal_novo_agendamento').css('display', 'none');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        
                        // Recarregar tabela
                        console.log('Recarregando tabela...');
                        try {
                            if (window.agendamentosDataTable) {
                                console.log('Usando referência global de DataTable');
                                window.agendamentosDataTable.ajax.reload(function() {
                                    console.log('Tabela recarregada com sucesso via callback!');
                                });
                            } else {
                                console.log('Referência global não encontrada, tentando obter DataTable');
                                const table = $('#table_agendamentos').DataTable();
                                table.ajax.reload(function() {
                                    console.log('Tabela recarregada com sucesso!');
                                });
                            }
                        } catch(e) {
                            console.error('Erro ao recarregar DataTable:', e);
                            console.log('Tentando recarregar a página em 2 segundos...');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                        
                        // Limpar formulários
                        limparFormularios();
                    } else {
                        console.log('Erro na resposta:', response.message);
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.log('Erro AJAX:', xhr);
                    alert('Erro ao salvar agendamento');
                }
            });
        }

        function limparFormularios() {
            console.log('Limpando formulários...');
            $('#form_cadastro_imovel')[0].reset();
            $('#form_dados_agendamento')[0].reset();
            $('#input_prestador_id').val('');
            $('#btn_proximo_dados').prop('disabled', true);
            
            // Voltar para aba de imóvel
            console.log('Voltando para aba de imóvel');
            $('.tab-pane').removeClass('show active');
            $('.nav-link').removeClass('active');
            $('#content_imovel').addClass('show active');
            $('#tab_imovel').addClass('active');
            
            console.log('Formulários limpos!');
        }
    </script>
@stop
