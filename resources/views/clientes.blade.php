{{-- Bloco de pesquisa --}}
<div class="clientes">
    <div class="right_col" role="main">
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                    <div class="form-group row">
                        <h6> Pesquisa</h6>
                        <form id="filtro" action="clientes" method="get" data-parsley-validate=""
                            class="form-horizontal form-label-left" novalidate="">
                            <div class="container">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="nome" class="col-sm-1 col-form-label">Nome</label>
                                        <input type="text" id="nome" name="nome" class="form-control"
                                            value="{{ $request->input('nome') ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ativo" class="col-sm-1 col-form-label">Situação</label>
                                        <select class="form-control" id="ativo" name="ativo">
                                            <option value="A"
                                                {{ $request->input('ativo') == 'A' ? 'selected' : '' }}>Ativo</option>
                                            <option value="I"
                                                {{ $request->input('ativo') == 'I' ? 'selected' : '' }}>Inativo</option>
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
                    <table id='table_clientes' class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th data-name="id">ID</th>
                                <th data-name="nome_empresa">Empresa</th>
                                <th data-name="nome">Nome</th>
                                <th data-name="ativo">Situação</th>
                                <th data-name="acoes">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INCLUIR --}}
    <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" aria-labelledby="modal_incluir"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <form method="POST" action="clientes/incluir">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Inclusão de Cliente</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                @csrf
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_incluir" href="#dados_clientes_incluir">Dados do
                                                cliente</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" id="id_dados_comerciais_incluir" href="#dados_comerciais_incluir">Dados comerciais</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_clientes_incluir" class="row col-md-12 dados" style="display:block;">

                                    @include('formularios.clientesFormularioClientes')

                                </div>
                                <div id="dados_comerciais_incluir" class="row col-md-12 dados" style="display:none;">

                                    @include('formularios.clientesFormularioComerciais')

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_incluir"
                        data-dismiss="modal">Fechar</button>
                        <button type="button" id="salvar_clientes_incluir"
                        class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ALTERAÇÃO --}}
    <div class="modal fade" id="modal_alteracao" tabindex="-1" role="dialog" aria-labelledby="modal_alteracao"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <form method="POST" action="clientes/alterar">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alteração de Cliente</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                <input type="hidden" id="modal_id" name="modal_id" value="">
                                @csrf
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_alterar" href="#dados_clientes">Dados do
                                                cliente</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#dados_comerciais">Dados
                                                comerciais</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_clientes" class="row col-md-12 dados" style="display:block;">

                                    @include('formularios.clientesFormularioClientes')

                                </div>
                                <div id="dados_comerciais" class="row col-md-12 dados" style="display:none;">

                                    @include('formularios.clientesFormularioComerciais')

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_alteracao"
                        data-dismiss="modal">Fechar</button>
                        <button type="button" id="salvar_clientes_alterar"
                        class="btn btn-primary desabilita_editar">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

