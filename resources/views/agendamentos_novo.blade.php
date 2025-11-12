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

        /* Modal Customizado */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
        }
        .modal.show {
            overflow-y: auto;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
            opacity: 0.5;
        }
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 90vw;
        }
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 0.3rem;
            outline: 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }
        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
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
                                    <th>Serviço</th>
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

                    <!-- CONTE�DO DAS ABAS -->
                    <div class="tab-content">
                        <!-- ABA 1: CADASTRO DE IM�VEL -->
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
                                            name="complemento_viacep">
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
                                    <div class="col-md-3">
                                        <label for="input_data_agendamento">Data *</label>
                                        <input type="date" class="form-control" id="input_data_agendamento" 
                                            name="data" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="input_hora_inicio">Hora Início *</label>
                                        <input type="time" class="form-control" id="input_hora_inicio" 
                                            name="hora_inicio" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="input_hora_fim">Hora Fim</label>
                                        <input type="time" class="form-control" id="input_hora_fim" name="hora_fim">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="input_servico_id">Serviço (Demanda) *</label>
                                        <select class="form-control" id="input_servico_id" name="servico_id" required>
                                            <option value="">Selecione o Serviço</option>
                                            @foreach($servicos ?? [] as $servico)
                                                <option value="{{ $servico->id }}">{{ $servico->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Informações SLA</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="input_data_criacao_demanda">Data Criação Demanda</label>
                                        <input type="datetime-local" class="form-control" id="input_data_criacao_demanda" 
                                            name="data_criacao_demanda" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input_data_vencimento_sla">Data Vencimento SLA</label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="input_data_vencimento_sla" 
                                                name="data_vencimento_sla" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" id="btn_calcular_sla">
                                                    <i class="fas fa-calculator"></i> Calcular
                                                </button>
                                            </div>
                                        </div>
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
    <!-- MODAL DE EDIÇÃO DE AGENDAMENTO -->
    <div class="modal fade" id="modal_editar_agendamento" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Editar Agendamento</h5>
                    <button type="button" class="close text-white" onclick="fecharModalEdicao()">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- ABAS PRINCIPAIS -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="edit_tab_imovel" data-toggle="tab" href="#edit_content_imovel" role="tab">
                                <i class="fas fa-home"></i> 1. Cadastro de Imóvel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="edit_tab_prestador" data-toggle="tab" href="#edit_content_prestador" role="tab">
                                <i class="fas fa-user-tie"></i> 2. Seleção de Prestador
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="edit_tab_dados" data-toggle="tab" href="#edit_content_dados" role="tab">
                                <i class="fas fa-list"></i> 3. Dados do Agendamento
                            </a>
                        </li>
                    </ul>

                    <!-- CONTEÚDO DAS ABAS -->
                    <div class="tab-content">
                        <!-- ABA 1: CADASTRO DE IMÓVEL -->
                        <div class="tab-pane fade show active" id="edit_content_imovel" role="tabpanel">
                            <form id="form_editar_imovel">
                                <input type="hidden" id="edit_agendamento_id">

                                <h6 class="text-info mb-3"><strong>Dados do Cliente</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="edit_cliente_select">Cliente *</label>
                                        <select class="form-control" id="edit_cliente_select" required>
                                            <option value="">Selecione um cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">
                                                    {{ $cliente->nome ?? $cliente->nome_empresa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Informações do Imóvel</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label><strong>Imóvel (Endereço Principal)</strong></label>
                                        <p id="edit_imovel_endereco" class="form-control-plaintext"></p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="edit_imovel_complemento"><strong>Complemento do Imóvel</strong> (AP 25 BLOCO 2, LT 25 QD D, etc)</label>
                                        <textarea class="form-control" id="edit_imovel_complemento" rows="3" placeholder="Ex: AP 25 BLOCO 2&#10;LT 25 QD D"></textarea>
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-secondary" onclick="fecharModalEdicao()">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="edit_btn_proximo_prestador">
                                        Próximo: Seleção de Prestador <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- ABA 2: SELEÇÃO DE PRESTADOR -->
                        <div class="tab-pane fade" id="edit_content_prestador" role="tabpanel">
                            <h6 class="text-info mb-3"><strong>Prestador Atual</strong></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label><strong>Prestador</strong></label>
                                    <p id="edit_prestador_nome" class="form-control-plaintext"></p>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <button type="button" class="btn btn-secondary" id="edit_btn_voltar_imovel">
                                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                                </button>
                                <button type="button" class="btn btn-primary" id="edit_btn_proximo_dados">
                                    Próximo: Dados do Agendamento <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ABA 3: DADOS DO AGENDAMENTO -->
                        <div class="tab-pane fade" id="edit_content_dados" role="tabpanel">
                            <form id="form_editar_dados">
                                <h6 class="text-info mb-3"><strong>Dados do Agendamento</strong></h6>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="edit_data">Data do Agendamento *</label>
                                        <input type="date" class="form-control" id="edit_data" required>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_servico_id">Serviço (Demanda) *</label>
                                        <select class="form-control" id="edit_servico_id" required>
                                            <option value="">Selecione o Serviço</option>
                                            @foreach($servicos ?? [] as $servico)
                                                <option value="{{ $servico->id }}">{{ $servico->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_data_vencimento_sla">Data Vencimento SLA</label>
                                        <input type="datetime-local" class="form-control" id="edit_data_vencimento_sla" readonly>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Informaç�es do Contato</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_contato">Nome do Contato</label>
                                        <input type="text" class="form-control" id="edit_contato">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_telefone">Telefone do Contato</label>
                                        <input type="text" class="form-control" id="edit_telefone">
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Deslocamento</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_deslocamento_valor">Valor Deslocamento (R$)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_deslocamento_valor" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_deslocamento_observacoes">Observações Deslocamento</label>
                                        <textarea class="form-control" id="edit_deslocamento_observacoes" rows="2"></textarea>
                                    </div>
                                </div>

                                <h6 class="text-info mb-3"><strong>Status</strong></h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" id="edit_status">
                                            <option value="CRIADO">Criado</option>
                                            <option value="ATRIBUIDO">Atribuído</option>
                                            <option value="REAGENDADO">Reagendado</option>
                                            <option value="CONCLUIDO">Concluído</option>
                                            <option value="CANCELADO">Cancelado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-secondary" onclick="fecharModalEdicao()">Cancelar</button>
                                    <button type="button" class="btn btn-secondary" id="edit_btn_voltar_prestador">
                                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                                    </button>
                                    <button type="button" class="btn btn-success" id="edit_btn_salvar">
                                        <i class="fas fa-save mr-2"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Garantir que jQuery esteja carregado antes dos scripts abaixo -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#input_cep').mask('00000-000');
            $('#input_numero_contato').mask('(00) 00000-0000');
            
            inicializarDataTable();
            setupEventListeners();
        });

        function inicializarDataTable() {
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
                    { data: 'servico' },
                    { data: 'prestador' },
                    { data: 'ativo' },
                    { data: 'acoes', orderable: false }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
                },
                autoWidth: false,
                pageLength: 10,
                searching: true,
                ordering: true,
                paging: true
            });
            
            window.agendamentosDataTable = table;
        }

        function setupEventListeners() {
            $('#modal_novo_agendamento').on('show.bs.modal', function () {
                limparFormularios();
            });

            $('#btn_consultar_cep').click(function() {
                consultarViaCep();
            });

            $('#btn_proximo_prestador').click(function() {
                if (validarFormularioImovel()) {
                    carregarPrestadoresRecomendados();
                }
            });

            $('#btn_voltar_imovel').click(function() {
                $('#modal_novo_agendamento .tab-pane').removeClass('show active');
                $('#modal_novo_agendamento .nav-link').removeClass('active');
                
                $('#modal_novo_agendamento #content_imovel').addClass('show active');
                $('#modal_novo_agendamento #tab_imovel').addClass('active');
            });

            $('#btn_proximo_dados').click(function() {
                $('#modal_novo_agendamento .tab-pane').removeClass('show active');
                $('#modal_novo_agendamento .nav-link').removeClass('active');
                
                $('#modal_novo_agendamento #content_dados').addClass('show active');
                $('#modal_novo_agendamento #tab_dados').addClass('active');
                
                const agora = new Date();
                const ano = agora.getFullYear();
                const mes = String(agora.getMonth() + 1).padStart(2, '0');
                const dia = String(agora.getDate()).padStart(2, '0');
                const hora = String(agora.getHours()).padStart(2, '0');
                const minuto = String(agora.getMinutes()).padStart(2, '0');
                const dataHoraAtual = `${ano}-${mes}-${dia}T${hora}:${minuto}`;
                
                setTimeout(function() {
                    const $campo = $('#input_data_criacao_demanda');
                    
                    $campo.val(dataHoraAtual);
                    $campo.prop('value', dataHoraAtual);
                    $campo[0].value = dataHoraAtual;
                    
                    $campo.trigger('change');
                    $campo.trigger('input');
                }, 100);
            });

            $('#btn_voltar_prestador').click(function() {
                $('#modal_novo_agendamento .tab-pane').removeClass('show active');
                $('#modal_novo_agendamento .nav-link').removeClass('active');
                
                $('#modal_novo_agendamento #content_prestador').addClass('show active');
                $('#modal_novo_agendamento #tab_prestador').addClass('active');
            });

            $('#btn_salvar_agendamento_novo').click(function() {
                salvarNovoAgendamento();
            });

            $(document).on('click', '[data-action="editar"]', function() {
                const agendamentoId = $(this).data('id');
                editarAgendamento(agendamentoId);
            });

            $(document).on('click', '[data-action="deletar"]', function() {
                const agendamentoId = $(this).data('id');
                deletarAgendamento(agendamentoId);
            });

            $('#edit_btn_proximo_prestador').click(function() {
                $('#modal_editar_agendamento .tab-pane').removeClass('show active');
                $('#modal_editar_agendamento .nav-link').removeClass('active');
                
                $('#modal_editar_agendamento #edit_content_prestador').addClass('show active');
                $('#modal_editar_agendamento #edit_tab_prestador').addClass('active');
            });

            $('#edit_btn_voltar_imovel').click(function() {
                $('#modal_editar_agendamento .tab-pane').removeClass('show active');
                $('#modal_editar_agendamento .nav-link').removeClass('active');
                
                $('#modal_editar_agendamento #edit_content_imovel').addClass('show active');
                $('#modal_editar_agendamento #edit_tab_imovel').addClass('active');
            });

            $('#edit_btn_proximo_dados').click(function() {
                $('#modal_editar_agendamento .tab-pane').removeClass('show active');
                $('#modal_editar_agendamento .nav-link').removeClass('active');
                
                $('#modal_editar_agendamento #edit_content_dados').addClass('show active');
                $('#modal_editar_agendamento #edit_tab_dados').addClass('active');
            });

            $('#edit_btn_voltar_prestador').click(function() {
                $('#modal_editar_agendamento .tab-pane').removeClass('show active');
                $('#modal_editar_agendamento .nav-link').removeClass('active');
                
                $('#modal_editar_agendamento #edit_content_prestador').addClass('show active');
                $('#modal_editar_agendamento #edit_tab_prestador').addClass('active');
            });

            $('#edit_btn_salvar').click(function() {
                salvarEdicaoAgendamento();
            });

            // Delegação de eventos para change do select de serviço
            $(document).on('change', '#input_servico_id', function() {
                console.log(' Change event triggered para input_servico_id');
                const dataCriacao = $('#input_data_criacao_demanda').val();
                const servicoId = $(this).val();
                console.log(' Dados:', {dataCriacao, servicoId});
                calcularDataVencimentoSLA(dataCriacao, servicoId);
            });

            $(document).on('change', '#edit_servico_id', function() {
                console.log(' Change event triggered para edit_servico_id');
                const data = $('#edit_data').val();
                const servicoId = $(this).val();
                console.log(' Dados:', {data, servicoId});
                calcularDataVencimentoSLAEdit(data, servicoId);
            });

            $(document).on('change', '#edit_data', function() {
                console.log(' Change event triggered para edit_data');
                const data = $(this).val();
                const servicoId = $('#edit_servico_id').val();
                console.log(' Dados:', {data, servicoId});
                calcularDataVencimentoSLAEdit(data, servicoId);
            });

            // Aguardar um tempo e tentar adicionar listeners diretos também
            setTimeout(function() {
                console.log('Adicionando listeners diretos...');
                
                const selectServico = document.getElementById('input_servico_id');
                if (selectServico) {
                    console.log('Elemento input_servico_id encontrado!');
                    
                    // Tentar múltiplos eventos
                    selectServico.addEventListener('change', function() {
                        console.log(' LISTENER DIRETO - change - input_servico_id mudou!');
                        const dataCriacao = document.getElementById('input_data_criacao_demanda').value;
                        const servicoId = this.value;
                        console.log(' Valores:', {dataCriacao, servicoId});
                        if (dataCriacao && servicoId) {
                            calcularDataVencimentoSLA(dataCriacao, servicoId);
                        }
                    });

                    selectServico.addEventListener('click', function() {
                        console.log(' LISTENER DIRETO - click - input_servico_id clicado!');
                    });

                    selectServico.addEventListener('input', function() {
                        console.log(' LISTENER DIRETO - input - input_servico_id mudou!');
                        const dataCriacao = document.getElementById('input_data_criacao_demanda').value;
                        const servicoId = this.value;
                        console.log(' Valores:', {dataCriacao, servicoId});
                        if (dataCriacao && servicoId) {
                            calcularDataVencimentoSLA(dataCriacao, servicoId);
                        }
                    });
                } else {
                    console.log(' Elemento input_servico_id NÃO encontrado!');
                }

                const editSelectServico = document.getElementById('edit_servico_id');
                if (editSelectServico) {
                    console.log(' Elemento edit_servico_id encontrado!');
                    
                    editSelectServico.addEventListener('change', function() {
                        console.log(' LISTENER DIRETO - change - edit_servico_id mudou!');
                        const data = document.getElementById('edit_data').value;
                        const servicoId = this.value;
                        console.log(' Valores:', {data, servicoId});
                        if (data && servicoId) {
                            calcularDataVencimentoSLAEdit(data, servicoId);
                        }
                    });

                    editSelectServico.addEventListener('input', function() {
                        console.log(' LISTENER DIRETO - input - edit_servico_id mudou!');
                        const data = document.getElementById('edit_data').value;
                        const servicoId = this.value;
                        console.log(' Valores:', {data, servicoId});
                        if (data && servicoId) {
                            calcularDataVencimentoSLAEdit(data, servicoId);
                        }
                    });
                } else {
                    console.log(' Elemento edit_servico_id NÃO encontrado!');
                }

                const editData = document.getElementById('edit_data');
                if (editData) {
                    console.log(' Elemento edit_data encontrado!');
                    
                    editData.addEventListener('change', function() {
                        console.log(' LISTENER DIRETO - change - edit_data mudou!');
                        const data = this.value;
                        const servicoId = document.getElementById('edit_servico_id').value;
                        console.log(' Valores:', {data, servicoId});
                        if (data && servicoId) {
                            calcularDataVencimentoSLAEdit(data, servicoId);
                        }
                    });
                } else {
                    console.log(' Elemento edit_data NÃO encontrado!');
                }
            }, 500);
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
                    if (response.success) {
                        $('#input_imovel_id').val(response.data.id);
                        carregarPrestadores(response.data.id);
                    } else {
                        alert('Erro ao cadastrar imóvel: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erro ao cadastrar imóvel');
                }
            });
        }

        function carregarPrestadores(imovelId) {
            const cep = $('#input_cep').val();
            const estado = $('#input_estado').val();
            const cidade = $('#input_cidade').val();
            
            const url = '/agendamentos/prestadores-recomendados?cep=' + encodeURIComponent(cep) + '&estado=' + encodeURIComponent(estado) + '&cidade=' + encodeURIComponent(cidade);
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        exibirPrestadores(response.data);
                        $('#modal_novo_agendamento .tab-pane').removeClass('show active');
                        $('#modal_novo_agendamento .nav-link').removeClass('active');
                        
                        $('#modal_novo_agendamento #content_prestador').addClass('show active');
                        $('#modal_novo_agendamento #tab_prestador').addClass('active');
                    } else {
                        $('#container_prestadores').html('<div class="alert alert-warning">Nenhum prestador encontrado para esta localização</div>');
                    }
                },
                error: function(xhr) {
                    alert('Erro ao carregar prestadores: ' + xhr.status);
                }
            });
        }

        function exibirPrestadores(prestadores) {
            let html = '';

            if (prestadores.length === 0) {
                html = '<div class="alert alert-warning">Nenhum prestador dispon�vel</div>';
            } else {
                prestadores.forEach(function(p) {
                    html += `
                        <div class="prestador-card" style="border: 1px solid #ddd; padding: 1rem; margin-bottom: 0.5rem; cursor: pointer;" onclick="selecionarPrestador(${p.id}, '${p.nome}')">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-2">${p.nome}</h6>
                                    <p class="mb-1"><small class="text-muted"> ${p.localizacao || 'Sem informação'}</small></p>
                                    <p class="mb-0"><small class="text-muted"> Avaliação: ${p.avaliacao || 'N/A'}</small></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h5 class="text-success mb-2">R$ ${p.valor_hora || 'N/A'}</h5>
                                    <p class="mb-0"><small> ${p.telefone || 'N/A'}</small></p>
                                    ${p.whatsapp ? `<p><small> WhatsApp</small></p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            $('#container_prestadores').html(html);
        }

        function selecionarPrestador(prestadorId, prestadorNome) {
            $('#input_prestador_id').val(prestadorId);

            $('.prestador-card').removeClass('selected');
            event.currentTarget.classList.add('selected');

            $('#btn_proximo_dados').prop('disabled', false);
        }

        function salvarNovoAgendamento() {
            const dados = {
                cliente_id: $('#input_cliente_id').val(),
                imovel_id: $('#input_imovel_id').val(),
                prestador_id: $('#input_prestador_id').val(),
                servico_id: $('#input_servico_id').val(),
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

            $.ajax({
                url: '/agendamentos/salva',
                type: 'POST',
                data: dados,
                success: function(response) {
                    if (response.success) {
                        alert('Agendamento salvo com sucesso!');
                        
                        $('#modal_novo_agendamento').css('display', 'none');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        
                        try {
                            if (window.agendamentosDataTable) {
                                window.agendamentosDataTable.ajax.reload(function() {
                                });
                            } else {
                                const table = $('#table_agendamentos').DataTable();
                                table.ajax.reload(function() {
                                });
                            }
                        } catch(e) {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                        
                        limparFormularios();
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erro ao salvar agendamento');
                }
            });
        }

        function limparFormularios() {
            $('#form_cadastro_imovel')[0].reset();
            $('#form_dados_agendamento')[0].reset();
            $('#input_prestador_id').val('');
            $('#btn_proximo_dados').prop('disabled', true);
            
            $('.tab-pane').removeClass('show active');
            $('.nav-link').removeClass('active');
            $('#content_imovel').addClass('show active');
            $('#tab_imovel').addClass('active');
        }

        function editarAgendamento(agendamentoId) {
            $.ajax({
                url: '/agendamentos/' + agendamentoId + '/editar',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const agendamento = response.data;
                        
                        $('#edit_agendamento_id').val(agendamento.id);
                        $('#edit_cliente_select').val(agendamento.cliente_id || '');
                        $('#edit_imovel_endereco').text(agendamento.imovel_endereco);
                        $('#edit_prestador_nome').text(agendamento.prestador_nome);
                        $('#edit_servico_id').val(agendamento.servico_id || '');
                        $('#edit_data').val(agendamento.data);
                        $('#edit_data_vencimento_sla').val(agendamento.data_vencimento_sla || '');
                        $('#edit_contato').val(agendamento.contato_nome || '');
                        $('#edit_telefone').val(agendamento.telefone_contato || '');
                        $('#edit_imovel_complemento').val(agendamento.imovel_complemento || '');
                        $('#edit_deslocamento_valor').val(agendamento.deslocamento_valor || 0);
                        $('#edit_deslocamento_observacoes').val(agendamento.deslocamento_observacoes || '');
                        $('#edit_status').val(agendamento.status || 'CRIADO');
                        
                        $('#modal_editar_agendamento').css('display', 'block').addClass('show');
                        $('body').addClass('modal-open');
                        $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                    } else {
                        alert('Erro ao carregar agendamento: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Erro ao carregar agendamento para edição: ' + status);
                }
            });
        }

        function fecharModalEdicao() {
            $('#modal_editar_agendamento').css('display', 'none').removeClass('show');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }

        function deletarAgendamento(agendamentoId) {
            console.log('deletarAgendamento() chamado com ID:', agendamentoId);
            
            if (!confirm('Tem certeza que deseja deletar este agendamento?')) {
                console.log('Deleção cancelada pelo usuário');
                return;
            }
            
            console.log('Enviando requisição POST para:', '/agendamentos/' + agendamentoId + '/deletar');
            
            $.ajax({
                url: '/agendamentos/' + agendamentoId + '/deletar',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('DELETE SUCCESS:', response);
                    if (response.success) {
                        console.log('Agendamento deletado com sucesso!');
                        alert('Agendamento deletado com sucesso!');
                        
                        // Recarregar tabela
                        console.log('Recarregando tabela...');
                        try {
                            if (window.agendamentosDataTable) {
                                window.agendamentosDataTable.ajax.reload(function() {
                                    console.log('Tabela recarregada com sucesso!');
                                });
                            } else {
                                const table = $('#table_agendamentos').DataTable();
                                table.ajax.reload(function() {
                                    console.log('Tabela recarregada com sucesso!');
                                });
                            }
                        } catch(e) {
                            console.error('Erro ao recarregar DataTable:', e);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        console.log('Erro na resposta:', response.message);
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('DELETE ERROR:', status, error, xhr);
                    console.error('Response:', xhr.responseText);
                    alert('Erro ao deletar agendamento: ' + status);
                }
            });
        }

        function salvarEdicaoAgendamento() {
            const agendamentoId = $('#edit_agendamento_id').val();
            const dados = {
                cliente_id: $('#edit_cliente_select').val(),
                contato_nome: $('#edit_contato').val(),
                telefone_contato: $('#edit_telefone').val(),
                servico_id: $('#edit_servico_id').val(),
                deslocamento_valor: $('#edit_deslocamento_valor').val(),
                deslocamento_observacoes: $('#edit_deslocamento_observacoes').val(),
                imovel_complemento: $('#edit_imovel_complemento').val(),
                data: $('#edit_data').val(),
                data_vencimento_sla: $('#edit_data_vencimento_sla').val(),
                status: $('#edit_status').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: '/agendamentos/' + agendamentoId + '/atualizar',
                type: 'POST',
                data: dados,
                success: function(response) {
                    if (response.success) {
                        alert('Agendamento atualizado com sucesso!');
                        
                        fecharModalEdicao();
                        
                        try {
                            if (window.agendamentosDataTable) {
                                window.agendamentosDataTable.ajax.reload();
                            } else {
                                const table = $('#table_agendamentos').DataTable();
                                table.ajax.reload();
                            }
                        } catch(e) {
                            location.reload();
                        }
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erro ao atualizar agendamento');
                }
            });
        }

        function calcularDataVencimentoSLA(dataCriacao, servicoId) {
            if (!dataCriacao || !servicoId) {
                console.log('calcularDataVencimentoSLA - Faltam parâmetros:', {dataCriacao, servicoId});
                return;
            }

            console.log('calcularDataVencimentoSLA - Buscando SLA para serviço:', servicoId);

            $.ajax({
                url: '/agendamentos/servico/' + servicoId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.success && response.data.sla) {
                        const slaEmDias = response.data.sla;
                        console.log('SLA em dias:', slaEmDias);
                        
                        const dataCriacao = new Date($('#input_data_criacao_demanda').val());
                        console.log('Data Criação:', dataCriacao);
                        
                        const dataVencimento = new Date(dataCriacao);
                        dataVencimento.setDate(dataVencimento.getDate() + slaEmDias);
                        console.log('Data Vencimento calculada:', dataVencimento);
                        
                        const ano = dataVencimento.getFullYear();
                        const mes = String(dataVencimento.getMonth() + 1).padStart(2, '0');
                        const dia = String(dataVencimento.getDate()).padStart(2, '0');
                        const hora = String(dataVencimento.getHours()).padStart(2, '0');
                        const minuto = String(dataVencimento.getMinutes()).padStart(2, '0');
                        const dataVencimentoFormatada = `${ano}-${mes}-${dia}T${hora}:${minuto}`;
                        
                        console.log('Data formatada:', dataVencimentoFormatada);
                        
                        $('#input_data_vencimento_sla').val(dataVencimentoFormatada);
                        console.log('Campo preenchido com:', $('#input_data_vencimento_sla').val());
                    } else {
                        console.error('Resposta inválida ou sem SLA');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao buscar SLA do serviço:', status, error, xhr.responseText);
                }
            });
        }

        function calcularDataVencimentoSLAEdit(data, servicoId) {
            if (!data || !servicoId) {
                console.log('calcularDataVencimentoSLAEdit - Faltam parâmetros:', {data, servicoId});
                return;
            }

            console.log('calcularDataVencimentoSLAEdit - Buscando SLA para serviço:', servicoId);

            $.ajax({
                url: '/agendamentos/servico/' + servicoId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.success && response.data.sla) {
                        const slaEmDias = response.data.sla;
                        console.log('SLA em dias:', slaEmDias);
                        
                        const dataCriacao = new Date(data + 'T00:00:00');
                        console.log('Data Criação:', dataCriacao);
                        
                        const dataVencimento = new Date(dataCriacao);
                        dataVencimento.setDate(dataVencimento.getDate() + slaEmDias);
                        console.log('Data Vencimento calculada:', dataVencimento);
                        
                        const ano = dataVencimento.getFullYear();
                        const mes = String(dataVencimento.getMonth() + 1).padStart(2, '0');
                        const dia = String(dataVencimento.getDate()).padStart(2, '0');
                        const hora = String(dataVencimento.getHours()).padStart(2, '0');
                        const minuto = String(dataVencimento.getMinutes()).padStart(2, '0');
                        const dataVencimentoFormatada = `${ano}-${mes}-${dia}T${hora}:${minuto}`;
                        
                        console.log('Data formatada:', dataVencimentoFormatada);
                        
                        $('#edit_data_vencimento_sla').val(dataVencimentoFormatada);
                        console.log('Campo preenchido com:', $('#edit_data_vencimento_sla').val());
                    } else {
                        console.error('Resposta inválida ou sem SLA');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao buscar SLA do serviço:', status, error, xhr.responseText);
                }
            });
        }
    </script>
@stop
