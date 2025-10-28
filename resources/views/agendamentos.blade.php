@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')
    <div class="form-group row">
        <h6 class="text-dark col-sm-8 col-form-label">Agendamentos</h6>
        <div class="col-sm-4">
            <p>
                <button id="btn_pesquisar" class="btn btn-outline-primary" type="button">Pesquisar</button>
                <button id="btn_adicionar_aba" class="btn btn-outline-success" type="button">Adicionar</button>
            </p>
        </div>
    </div>
@stop

@section('content')
    <div class="right_col" role="main">
        <!-- Abas -->
        <div class="form-group row">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active aba-nav" href="#tab_agendamentos" role="tab">
                        <i class="fas fa-calendar"></i> Agendamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link aba-nav" href="#tab_imoveis" role="tab">
                        <i class="fas fa-home"></i> Imóveis
                    </a>
                </li>
            </ul>
        </div>

        <!-- Conteúdo das Abas -->
        <div class="tab-content">
            <!-- ABA 1: AGENDAMENTOS -->
            <div class="aba-conteudo" id="tab_agendamentos" style="display:block;">
                <div class="row">
                    <div class="col">
                        <div class="collapse" id="div_pesquisa">
                            <div class="border p-3 mb-3">
                                <h6>Pesquisa de Agendamentos</h6>
                                <form id="filtro" action="/agendamentos" method="get">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Nº Sequencial</label>
                                            <input type="text" name="numero_sequencial" class="form-control" placeholder="Digite o número">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Cliente</label>
                                            <select name="cliente_id" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Status</label>
                                            <select name="ativo" class="form-control">
                                                <option value="">Todos</option>
                                                <option value="1">Ativo</option>
                                                <option value="0">Inativo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success">Buscar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div class="x_panel">
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
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA 2: IMÓVEIS -->
            <div class="aba-conteudo" id="tab_imoveis" style="display:none;">
                <div class="row">
                    <div class="col">
                        <div class="collapse" id="div_pesquisa_imovel">
                            <div class="border p-3 mb-3">
                                <h6>Pesquisa de Imóveis</h6>
                                <form id="filtro_imovel" action="/agendamentos" method="get">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Nome do Imóvel</label>
                                            <input type="text" name="nome" class="form-control" placeholder="Digite o nome">
                                        </div>
                                        <div class="col-md-5">
                                            <label>Cliente</label>
                                            <select name="cliente_id" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success">Buscar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_content">
                                <table id='table_imoveis' class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Endereço</th>
                                            <th>Comp</th>
                                            <th>Bairro</th>
                                            <th>Cidade</th>
                                            <th>UF</th>
                                            <th>Tipo</th>
                                            <th>Telefone</th>
                                            <th>Contato</th>
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
                    <div class="modal-body">
                        <input type="hidden" name="id" id="modal_id" value="">
                        
                        <!-- SEÇÃO: DADOS DO CLIENTE -->
                        <h6 class="text-info mb-3"><strong>Dados do Cliente</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cliente *</label>
                                    <select name="cliente_id" id="modal_cliente_id" class="form-control" required>
                                        <option value="">Selecione</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Imóvel *</label>
                                    <select name="imovel_id" id="modal_imovel_id" class="form-control" required>
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO: DADOS DO AGENDAMENTO -->
                        <h6 class="text-info mb-3 mt-3"><strong>Dados do Agendamento</strong></h6>
                        <div class="row">
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

                        <!-- SEÇÃO: CONTATO E PROPOSTA -->
                        <h6 class="text-info mb-3 mt-3"><strong>Contato e Proposta</strong></h6>
                        <div class="row">
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

                        <!-- SEÇÃO: DOCUMENTOS DE APOIO -->
                        <h6 class="text-info mb-3 mt-3"><strong>Documentos de Apoio</strong></h6>
                        <div class="row">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        var currentTab = 'agendamentos';
        var tableAgendamentos, tableImoveis;

        // Controlar abas
        $(document).on('click', '.aba-nav', function (e) {
            e.preventDefault();
            console.log('Clicou na aba');
            $('.aba-nav').removeClass('active');
            $(this).addClass('active');
            $('.aba-conteudo').hide();

            var target = $(this).attr('href');
            $(target).show();
            
            if (target === '#tab_agendamentos') {
                currentTab = 'agendamentos';
            } else if (target === '#tab_imoveis') {
                currentTab = 'imoveis';
            }
            console.log('CurrentTab agora é:', currentTab);
        });

        function initializeApp() {
            console.log('Script iniciado');
            
            tableAgendamentos = $('#table_agendamentos').DataTable({
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

            tableImoveis = $('#table_imoveis').DataTable({
                ajax: '/ajax/imoveis',
                columns: [
                    {data: 'endereco'},
                    {data: 'complemento'},
                    {data: 'bairro'},
                    {data: 'cidade'},
                    {data: 'uf'},
                    {data: 'tipo'},
                    {data: 'telefone'},
                    {data: 'contato'},
                    {data: 'acoes', orderable: false}
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                }
            });

            $('#btn_pesquisar').click(function(e) {
                e.preventDefault();
                console.log('Pesquisar clicado. Tab atual:', currentTab);
                if(currentTab === 'agendamentos') {
                    $('#div_pesquisa').slideToggle();
                } else if(currentTab === 'imoveis') {
                    $('#div_pesquisa_imovel').slideToggle();
                }
            });

            $('#btn_adicionar_aba').click(function(e) {
                e.preventDefault();
                console.log('Adicionar clicado. Tab atual:', currentTab);
                
                if(currentTab === 'agendamentos') {
                    console.log('Abrindo modal de AGENDAMENTO');
                    $('#form_incluir')[0].reset();
                    $('#modal_id').val('');
                    var modal = new bootstrap.Modal(document.getElementById('modal_incluir'));
                    modal.show();
                } else if(currentTab === 'imoveis') {
                    console.log('Abrindo modal de IMÓVEL');
                    $('#form_imovel')[0].reset();
                    $('#modal_imovel_hidden').val('');
                    var modal = new bootstrap.Modal(document.getElementById('modal_imovel'));
                    modal.show();
                }
            });

            $('#modal_cliente_id').on('change', function() {
                var clienteId = $(this).val();
                console.log('Cliente selecionado:', clienteId);
                if (!clienteId) {
                    $('#modal_imovel_id').html('<option value="">Selecione</option>');
                    return;
                }
                $.get('/api/imoveis-cliente/' + clienteId, function(res) {
                    console.log('Resposta de imóveis:', res);
                    var html = '<option value="">Selecione</option>';
                    if (res.data && Array.isArray(res.data)) {
                        res.data.forEach(function(im) {
                            html += '<option value="' + im.id + '">' + im.nome + '</option>';
                        });
                    }
                    $('#modal_imovel_id').html(html);
                });
            });

            $('#btn_salvar_agendamento').click(function(e) {
                e.preventDefault();
                console.log('Salvando agendamento');
                $.post('/agendamentos/salva', $('#form_incluir').serialize(), function(res) {
                    console.log('Resposta:', res);
                    if (res.success) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modal_incluir'));
                        modal.hide();
                        tableAgendamentos.ajax.reload();
                        alert('Agendamento salvo com sucesso!');
                    } else {
                        alert('Erro ao salvar: ' + (res.message || 'Desconhecido'));
                    }
                }).fail(function(error) {
                    console.error('Erro AJAX:', error);
                    alert('Erro ao salvar agendamento');
                });
            });

            $('#btn_salvar_imovel').click(function(e) {
                e.preventDefault();
                console.log('Salvando imóvel');
                $.post('/imoveis/salva', $('#form_imovel').serialize(), function(res) {
                    console.log('Resposta:', res);
                    if (res.success) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modal_imovel'));
                        modal.hide();
                        tableImoveis.ajax.reload();
                        alert('Imóvel salvo com sucesso!');
                    } else {
                        alert('Erro ao salvar: ' + (res.message || 'Desconhecido'));
                    }
                }).fail(function(error) {
                    console.error('Erro AJAX:', error);
                    alert('Erro ao salvar imóvel');
                });
            });

            $(document).on('click', '#table_agendamentos button[data-action="deletar"]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (confirm('Tem certeza que deseja deletar este agendamento?')) {
                    $.post('/agendamentos/deletar', {id: id, _token: $('meta[name="csrf-token"]').attr('content')}, function(res) {
                        if (res.success) {
                            tableAgendamentos.ajax.reload();
                            alert('Agendamento deletado com sucesso!');
                        } else {
                            alert('Erro ao deletar: ' + (res.message || 'Desconhecido'));
                        }
                    }).fail(function(error) {
                        console.error('Erro AJAX:', error);
                        alert('Erro ao deletar agendamento');
                    });
                }
            });

            $(document).on('click', '#table_agendamentos button[data-action="editar"]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                console.log('Editando agendamento:', id);
                $.get('/agendamentos/' + id, function(res) {
                    console.log('Dados do agendamento:', res);
                    if (res.success && res.data) {
                        var agendamento = res.data;
                        $('#modal_id').val(agendamento.id);
                        $('#modal_cliente_id').val(agendamento.cliente_id);
                        $('#modal_imovel_id').val(agendamento.imovel_id);
                        $('#modal_os').val(agendamento.numero_sequencial);
                        $('#modal_data').val(agendamento.data);
                        $('#modal_hora_inicio').val(agendamento.hora_inicio);
                        $('#modal_hora_fim').val(agendamento.hora_fim);
                        $('#modal_prestador_id').val(agendamento.prestador_id);
                        $('#modal_tipo_demanda').val(agendamento.tipo_demanda);
                        $('#modal_numero_contato').val(agendamento.numero_contato);
                        $('#modal_numero_proposta').val(agendamento.numero_proposta);
                        $('#modal_observacoes').val(agendamento.observacoes);
                        
                        var modal = new bootstrap.Modal(document.getElementById('modal_incluir'));
                        modal.show();
                    } else {
                        alert('Erro ao carregar agendamento');
                    }
                }).fail(function(error) {
                    console.error('Erro AJAX:', error);
                    alert('Erro ao carregar agendamento');
                });
            });

            $(document).on('click', '#table_imoveis button[data-action="deletar"]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (confirm('Tem certeza que deseja deletar este imóvel?')) {
                    $.post('/imoveis/deletar', {id: id, _token: $('meta[name="csrf-token"]').attr('content')}, function(res) {
                        if (res.success) {
                            tableImoveis.ajax.reload();
                            alert('Imóvel deletado com sucesso!');
                        } else {
                            alert('Erro ao deletar: ' + (res.message || 'Desconhecido'));
                        }
                    }).fail(function(error) {
                        console.error('Erro AJAX:', error);
                        alert('Erro ao deletar imóvel');
                    });
                }
            });

            $(document).on('click', '#table_imoveis button[data-action="editar"]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                console.log('Editando imóvel:', id);
                $.get('/imoveis/' + id, function(res) {
                    console.log('Dados do imóvel:', res);
                    if (res.success && res.data) {
                        var imovel = res.data;
                        $('#modal_imovel_hidden').val(imovel.id);
                        $('#modal_imovel_endereco').val(imovel.endereco);
                        $('#modal_imovel_complemento').val(imovel.complemento);
                        $('#modal_imovel_bairro').val(imovel.bairro);
                        $('#modal_imovel_cidade').val(imovel.cidade);
                        $('#modal_imovel_uf').val(imovel.estado || imovel.uf);
                        $('#modal_imovel_tipo').val(imovel.tipo);
                        $('#modal_imovel_telefone').val(imovel.telefone);
                        $('#modal_imovel_responsavel').val(imovel.responsavel);
                        
                        var modal = new bootstrap.Modal(document.getElementById('modal_imovel'));
                        modal.show();
                    } else {
                        alert('Erro ao carregar imóvel');
                    }
                }).fail(function(error) {
                    console.error('Erro AJAX:', error);
                    alert('Erro ao carregar imóvel');
                });
            });
        }

        if(document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeApp);
        } else {
            initializeApp();
        }
    </script>
@endsection
