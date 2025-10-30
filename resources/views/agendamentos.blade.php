@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')
    <div class="form-group row">
        <h6 class="text-dark col-sm-8 col-form-label">Agendamentos</h6>
        <div class="col-sm-4">
            <p>
                <button class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#div_pesquisa" aria-expanded="false" aria-controls="div_pesquisa">Pesquisar</button>
                <button class="btn btn-outline-success acao_abrir_modal_incluir" type="button" data-toggle="modal" data-target="#modal_incluir" aria-expanded="false" aria-controls="modal_incluir">Adicionar</button>
            </p>
        </div>
    </div>
@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link  rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">
    <style>
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
        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
        }
    </style>
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
    {{-- Bloco de pesquisa --}}
    <div class="agendamentos">
        <div class="right_col" role="main">
            <div class="row">
                <div class="col">
                    <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                        <div class="form-group row">
                            <h6>Pesquisa</h6>
                            <form id="filtro" action="/agendamentos" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                                <div class="container">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="numero_sequencial" class="col-sm-1 col-form-label">Nº Sequencial</label>
                                            <input type="text" id="numero_sequencial" name="numero_sequencial" class="form-control" value="{{ $request->input('numero_sequencial') ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cliente_id" class="col-sm-1 col-form-label">Cliente</label>
                                            <select class="form-control" id="cliente_id" name="cliente_id">
                                                <option value="">Todos</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}" {{ $request->input('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome ?? $cliente->nome_empresa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="col-sm-1 col-form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-success">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h6>Encontrados</h6>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id='table_agendamentos' class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th>OS</th>
                                    <th>Cliente</th>
                                    <th>Imóvel</th>
                                    <th>Endereço</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Técnico</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL INCLUIR AGENDAMENTO -->
    <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form_incluir" action="/agendamentos/salva" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="modal_id" value="">
                    <input type="hidden" name="imovel_id" id="modal_imovel_id" value="">
                    
                    <div class="modal-body">
                        <!-- ABAS -->
                        <ul class="nav nav-tabs mb-3" style="border-bottom: 2px solid #dee2e6;">
                            <li class="nav-item">
                                <a class="nav-link active modal_nave" href="#dados_agendamento_incluir" style="font-weight: 500; font-size: 15px;">Dados do Agendamento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link modal_nave" href="#dados_imovel_incluir" style="font-weight: 500; font-size: 15px;">Dados do Imóvel</a>
                            </li>
                        </ul>

                        <!-- ABA 1: DADOS DO AGENDAMENTO -->
                        <div id="dados_agendamento_incluir" class="dados" style="display:block;">
                            <!-- DADOS DO CLIENTE -->
                            <div class="row">
                                <h6 class="text-info mb-3 col-md-12"><strong>Dados do Cliente</strong></h6>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente *</label>
                                        <select name="cliente_id" id="modal_cliente_id" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nome ?? $cliente->nome_empresa }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- DADOS DO AGENDAMENTO -->
                            <div class="row">
                                <h6 class="text-info mb-3 col-md-12"><strong>Dados do Agendamento</strong></h6>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>OS (Sequencial) *</label>
                                        <input type="text" name="os" id="modal_os" class="form-control" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data *</label>
                                        <input type="date" name="data" id="modal_data" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Hora Início *</label>
                                        <input type="time" name="hora_inicio" id="modal_hora_inicio" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Hora Fim</label>
                                        <input type="time" name="hora_fim" id="modal_hora_fim" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Técnico Responsável (Prestador) *</label>
                                        <select name="prestador_id" id="modal_prestador_id" class="form-control" required>
                                            <option value="">Selecione</option>
                                            @if(isset($prestadores))
                                                @foreach($prestadores as $prestador)
                                                    <option value="{{ $prestador->id }}">{{ $prestador->nome }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo de Demanda</label>
                                        <select name="tipo_demanda" id="modal_tipo_demanda" class="form-control">
                                            <option value="">Selecione</option>
                                            <option value="vistoria">Vistoria</option>
                                            <option value="manutencao">Manutenção</option>
                                            <option value="reparo">Reparo</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTATO E PROPOSTA -->
                            <div class="row">
                                <h6 class="text-info mb-3 col-md-12"><strong>Contato e Proposta</strong></h6>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Número do Contato</label>
                                        <input type="text" name="numero_contato" id="modal_numero_contato" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Número da Proposta</label>
                                        <input type="text" name="numero_proposta" id="modal_numero_proposta" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- DOCUMENTOS DE APOIO -->
                            <div class="row">
                                <h6 class="text-info mb-3 col-md-12"><strong>Documentos de Apoio</strong></h6>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Matrícula</label>
                                        <input type="file" name="matricula" id="modal_matricula" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>IPTU</label>
                                        <input type="file" name="iptu" id="modal_iptu" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="observacoes" id="modal_observacoes" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA 2: DADOS DO IMÓVEL -->
                        <div id="dados_imovel_incluir" class="dados" style="display:none;">
                            <form id="form_imovel_inline">
                                <!-- LOCALIZAÇÃO -->
                                <div class="row">
                                    <h6 class="text-info mb-3 col-md-12"><strong>Localização</strong></h6>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Endereço *</label>
                                            <input type="text" name="endereco" id="form_imovel_inline_endereco" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Complemento</label>
                                            <input type="text" name="complemento" id="form_imovel_inline_complemento" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bairro</label>
                                            <input type="text" name="bairro" id="form_imovel_inline_bairro" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cidade</label>
                                            <input type="text" name="cidade" id="form_imovel_inline_cidade" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>UF</label>
                                            <input type="text" name="uf" id="form_imovel_inline_uf" class="form-control" maxlength="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tipo</label>
                                            <input type="text" name="tipo" id="form_imovel_inline_tipo" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <!-- CONTATO -->
                                <div class="row">
                                    <h6 class="text-info mb-3 col-md-12"><strong>Contato</strong></h6>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telefone</label>
                                            <input type="text" name="telefone" id="form_imovel_inline_telefone" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Responsável</label>
                                            <input type="text" name="responsavel" id="form_imovel_inline_responsavel" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btn_salvar_agendamento" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL INCLUIR IMÓVEL -->
    <div class="modal fade" id="modal_imovel" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Imóvel</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form_imovel" action="/imoveis/salva" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="modal_imovel_hidden" value="">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Endereço *</label>
                                    <input type="text" name="endereco" id="modal_imovel_endereco" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Complemento</label>
                                    <input type="text" name="complemento" id="modal_imovel_complemento" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bairro</label>
                                    <input type="text" name="bairro" id="modal_imovel_bairro" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <input type="text" name="cidade" id="modal_imovel_cidade" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>UF</label>
                                    <input type="text" name="uf" id="modal_imovel_uf" class="form-control" maxlength="2">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <input type="text" name="tipo" id="modal_imovel_tipo" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="text" name="telefone" id="modal_imovel_telefone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contato (Responsável)</label>
                                    <input type="text" name="responsavel" id="modal_imovel_responsavel" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btn_salvar_imovel" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/main_custom.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/acoes.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/dataTableAcoes.js') }}?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            // Controlar abas do modal
            $('.modal_nave').click(function(e) {
                e.preventDefault();
                let target = $(this).attr('href');
                
                // Remove active de todas as abas
                $('.modal_nave').removeClass('active');
                // Adiciona active a aba clicada
                $(this).addClass('active');
                
                // Esconde todos os conteúdos
                $('.dados').hide();
                // Mostra o conteúdo da aba clicada
                $(target).fadeIn();
            });

            let tableAgendamentos = $('#table_agendamentos').DataTable({
                ajax: '/ajax/agendamentos',
                columns: [
                    {data: 'numero_sequencial'},
                    {data: 'cliente'},
                    {data: 'imovel'},
                    {data: 'endereco'},
                    {data: 'data'},
                    {data: 'hora_inicio'},
                    {data: 'prestador'},
                    {data: 'ativo'},
                    {data: 'acoes', orderable: false}
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                }
            });

            // Abrir modal para novo agendamento
            $('.acao_abrir_modal_incluir').click(function() {
                $('#form_incluir')[0].reset();
                $('#modal_id').val('');
                // Garantir que a primeira aba está ativa
                $('.modal_nave').removeClass('active');
                $('.modal_nave:first').addClass('active');
                $('.dados').hide();
                $('#dados_agendamento_incluir').show();
            });

            // Carregar imóveis ao selecionar cliente
            $('#modal_cliente_id').on('change', function() {
                let clienteId = $(this).val();
                if (!clienteId) {
                    // Limpar dados do imóvel
                    $('#form_imovel_inline_endereco, #form_imovel_inline_complemento, #form_imovel_inline_bairro, #form_imovel_inline_cidade, #form_imovel_inline_uf, #form_imovel_inline_tipo, #form_imovel_inline_telefone, #form_imovel_inline_responsavel').val('');
                    return;
                }
                // Limpar campos para novo cadastro (se existe o formulário)
                let inputs = $('#dados_imovel_incluir input[type="text"]');
                inputs.val('');
            });

            // Salvar agendamento (que incluirá o imóvel)
            $('#btn_salvar_agendamento').on('click', function(e) {
                e.preventDefault();
                
                let clienteId = $('#modal_cliente_id').val();
                let endereco = $('#form_imovel_inline_endereco').val();
                
                if (!clienteId) {
                    alert('Selecione um cliente!');
                    return;
                }
                
                if (!endereco) {
                    alert('Preencha o endereço do imóvel!');
                    return;
                }

                // Montar dados do formulário com dados do imóvel
                let formData = $('#form_incluir').serialize() + 
                    '&imovel_endereco=' + encodeURIComponent($('#form_imovel_inline_endereco').val()) +
                    '&imovel_complemento=' + encodeURIComponent($('#form_imovel_inline_complemento').val()) +
                    '&imovel_bairro=' + encodeURIComponent($('#form_imovel_inline_bairro').val()) +
                    '&imovel_cidade=' + encodeURIComponent($('#form_imovel_inline_cidade').val()) +
                    '&imovel_uf=' + encodeURIComponent($('#form_imovel_inline_uf').val()) +
                    '&imovel_tipo=' + encodeURIComponent($('#form_imovel_inline_tipo').val()) +
                    '&imovel_telefone=' + encodeURIComponent($('#form_imovel_inline_telefone').val()) +
                    '&imovel_responsavel=' + encodeURIComponent($('#form_imovel_inline_responsavel').val());

                $.post('/agendamentos/salva', formData, function(res) {
                    if (res.success) {
                        // Tentar fechar modal com Bootstrap
                        try {
                            if (typeof $.fn.modal !== 'undefined') {
                                $('#modal_incluir').modal('hide');
                            } else {
                                // Fallback: remover atributo show e esconder com jQuery
                                $('#modal_incluir').removeClass('show').hide();
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                            }
                        } catch (e) {
                            $('#modal_incluir').hide();
                            $('.modal-backdrop').remove();
                        }
                        
                        tableAgendamentos.ajax.reload();
                        alert('Agendamento e imóvel salvos com sucesso!');
                    } else {
                        alert('Erro ao salvar: ' + (res.message || 'Erro desconhecido'));
                    }
                }).fail(function(error) {
                    alert('Erro ao salvar agendamento: ' + error.statusText);
                });
            });

            // Editar agendamento
            $(document).on('click', 'button[data-action="editar"][data-type="agendamento"]', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.get('/agendamentos/' + id, function(res) {
                    if (res.success && res.data) {
                        let agendamento = res.data;
                        $('#modal_id').val(agendamento.id);
                        $('#modal_cliente_id').val(agendamento.cliente_id).trigger('change');
                        setTimeout(() => {
                            $('#modal_imovel_id').val(agendamento.imovel_id).trigger('change');
                        }, 200);
                        $('#modal_os').val(agendamento.numero_sequencial);
                        $('#modal_data').val(agendamento.data);
                        $('#modal_hora_inicio').val(agendamento.hora_inicio);
                        $('#modal_hora_fim').val(agendamento.hora_fim);
                        $('#modal_prestador_id').val(agendamento.prestador_id);
                        $('#modal_tipo_demanda').val(agendamento.tipo_demanda);
                        $('#modal_numero_contato').val(agendamento.numero_contato);
                        $('#modal_numero_proposta').val(agendamento.numero_proposta);
                        $('#modal_observacoes').val(agendamento.observacoes);
                        
                        // Resetar abas para primeira
                        $('.modal_nave').removeClass('active');
                        $('.modal_nave:first').addClass('active');
                        $('.dados').hide();
                        $('#dados_agendamento_incluir').show();
                        
                        // Abrir modal com fallback
                        try {
                            if (typeof $.fn.modal !== 'undefined') {
                                $('#modal_incluir').modal('show');
                            } else {
                                $('#modal_incluir').addClass('show').fadeIn();
                                $('body').append('<div class="modal-backdrop fade show"></div>');
                                $('body').addClass('modal-open');
                            }
                        } catch (e) {
                            $('#modal_incluir').show();
                        }
                    }
                });
            });

            // Deletar agendamento
            $(document).on('click', 'button[data-action="deletar"][data-type="agendamento"]', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                if (confirm('Tem certeza que deseja deletar este agendamento?')) {
                    $.post('/agendamentos/deletar', {id: id, _token: $('meta[name="csrf-token"]').attr('content')}, function(res) {
                        if (res.success) {
                            tableAgendamentos.ajax.reload();
                            alert('Agendamento deletado com sucesso!');
                        } else {
                            alert('Erro ao deletar');
                        }
                    });
                }
            });

            // Fechar modal com botão cancelar
            $(document).on('click', '[data-dismiss="modal"]', function(e) {
                let modal = $(this).closest('.modal');
                if (modal.length) {
                    try {
                        if (typeof $.fn.modal !== 'undefined') {
                            modal.modal('hide');
                        } else {
                            modal.removeClass('show').hide();
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        }
                    } catch (e) {
                        modal.hide();
                        $('.modal-backdrop').remove();
                    }
                }
            });

            // Fechar modal ao clicar no X
            $(document).on('click', '.modal .close', function(e) {
                e.preventDefault();
                let modal = $(this).closest('.modal');
                if (modal.length) {
                    try {
                        if (typeof $.fn.modal !== 'undefined') {
                            modal.modal('hide');
                        } else {
                            modal.removeClass('show').hide();
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        }
                    } catch (e) {
                        modal.hide();
                        $('.modal-backdrop').remove();
                    }
                }
            });
        });
    </script>
@endsection
