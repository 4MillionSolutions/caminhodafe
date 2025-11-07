{{-- Bloco de pesquisa --}}
<div class="servicos">
    <div class="right_col" role="main">
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                    <div class="form-group row">
                        <h6> Pesquisa</h6>
                        <form id="filtro" action="servicos" method="get" data-parsley-validate=""
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
                    <table id='table_servicos' class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Situação</th>
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

    {{-- MODAL INCLUIR --}}
    <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" aria-labelledby="modal_incluir"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <form method="POST" action="servicos/incluir">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Inclusão de serviço</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                @csrf
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_incluir" href="#dados_servicos_incluir">Dados do
                                                servico</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_servicos_incluir" class="row col-md-12 dados" style="display:block;">
                                    <div class="row mt-2">
                                        <div class="col-md-8">
                                            <label for="nome" class="form-label">Nome</label>
                                            <input type="text" class="form-control " id="modal_nome"
                                                name="nome" maxlength="180" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label for="prioridade" class="form-label">Prioridade</label>
                                            <select class="form-control" id="modal_prioridade" name="prioridade">
                                                <option value="0">Selecione</option>
                                                <option value="1">Baixa</option>
                                                <option value="2">Média</option>
                                                <option value="3">Alta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label for="modal_sla" class="form-label">SLA</label>
                                            <select class="form-control" id="modal_sla" name="sla">
                                                <option value="0">D+0</option>
                                                <option value="1">D+1</option>
                                                <option value="2">D+2</option>
                                                <option value="3">D+3</option>
                                                <option value="4">D+4</option>
                                                <option value="5">D+5</option>
                                                <option value="6">D+6</option>
                                                <option value="7">D+7</option>
                                                <option value="8">D+8</option>
                                                <option value="9">D+9</option>
                                                <option value="10">D+10</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- //valor --}}
                                    <div class="row mt-2">
                                        <div class="col-md-2">
                                            <label for="modal_valor" class="form-label">Valor</label>
                                            <input type="text" class="form-control mask_valor" id="modal_valor" name="valor" maxlength="15" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="modal_status"
                                                name="status" checked>
                                            <label class="form-check-label" for="status">Ativo</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_incluir"
                        data-dismiss="modal">Fechar</button>
                        <button type="button" id="salvar_servicos_incluir"
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
            <form method="POST" action="servicos/alterar">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alteração de serviço</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                <input type="hidden" id="modal_id" name="modal_id" value="">
                                @csrf
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_alterar" href="#dados_servicos">Dados do
                                                serviço</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_servicos" class="row col-md-12 dados" style="display:block;">
                                    <div class="row mt-2">
                                        <div class="col-md-8">
                                            <label for="nome" class="form-label">Nome</label>
                                            <input type="text" class="form-control " id="modal_nome"
                                                name="nome" maxlength="180" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label for="prioridade" class="form-label">Prioridade</label>
                                            <select class="form-control" id="modal_prioridade" name="prioridade">
                                                <option value="0">Selecione</option>
                                                <option value="1">Baixa</option>
                                                <option value="2">Média</option>
                                                <option value="3">Alta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label for="modal_sla" class="form-label">SLA</label>
                                            <select class="form-control" id="modal_sla" name="sla">
                                                <option value="0">D+0</option>
                                                <option value="1">D+1</option>
                                                <option value="2">D+2</option>
                                                <option value="3">D+3</option>
                                                <option value="4">D+4</option>
                                                <option value="5">D+5</option>
                                                <option value="6">D+6</option>
                                                <option value="7">D+7</option>
                                                <option value="8">D+8</option>
                                                <option value="9">D+9</option>
                                                <option value="10">D+10</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- //valor --}}
                                    <div class="row mt-2">
                                        <div class="col-md-2">
                                            <label for="modal_valor" class="form-label">Valor</label>
                                            <input type="text" class="form-control mask_valor" id="modal_valor" name="valor" maxlength="15" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="modal_status"
                                                name="status" checked>
                                            <label class="form-check-label" for="status">Ativo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_alteracao"
                        data-dismiss="modal">Fechar</button>
                        <button type="button" id="salvar_servicos_alterar"
                        class="btn btn-primary habilita_editar">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
