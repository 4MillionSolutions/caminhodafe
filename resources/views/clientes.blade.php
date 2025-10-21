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
            <form method="POST" action="clientes/incluir">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Inclusão de Cliente</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                <input type="hidden" name="modal_id" value="">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="cliente" class="form-label">Nome*</label>
                                        <input type="text" class="form-control" id="modal_nome" name="nome"
                                            maxlength="200" required value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_incluir"
                            data-dismiss="modal">Fechar</button>
                        <button type="button" id="salvar_clientes_incluir" class="btn btn-primary">Confirmar</button>
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
                                            <a class="nav-link active modal_nave" href="#dados_clientes">Dados do
                                                cliente</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#dados_comerciais">Dados
                                                comerciais</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_clientes" class="row col-md-12 dados" style="display:block;">
                                    <div class="row mt-2">

                                        <div class="col-md-12">
                                            <label for="Tipo pessoa" class="form-label">Tipo</label>
                                            <input class="ml-2 tipo_pessoa" type="radio" id="modal_tipo_fisica"
                                                name="tipo" value="F" checked> Pessoa física
                                            <input class="ml-2 tipo_pessoa" type="radio" id="modal_tipo_juridica"
                                                name="tipo" value="J"> Pessoa jurídica
                                        </div>
                                        <div class="col-md-8">
                                            <label for="nome" class="form-label">Nome</label>
                                            <input type="text" class="form-control " id="nome"
                                                name="nome" maxlength="180" value="">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="documento" class="form-label label_documento">CPF</label>
                                            <input type="text" class="form-control cpf" id="documento"
                                                name="documento" maxlength="14" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cep" class="form-label">CEP</label>
                                            <input type="text" class="form-control cep" id="cep"
                                                name="cep" maxlength="8" value="{{ $cientes[0]->cep ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="endereco" class="form-label">Endereço</label>
                                            <input type="text" class="form-control " id="endereco"
                                                name="endereco" maxlength="500" value="">
                                        </div>
                                        <div class="col-md-1">
                                            <label for="numero" class="form-label">Número</label>
                                            <input type="text" class="form-control " id="numero"
                                                name="numero" maxlength="20" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="bairro" class="form-label">Bairro</label>
                                            <input type="text" class="form-control " id="bairro"
                                                name="bairro" maxlength="100" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="complemento" class="form-label">Complemento</label>
                                            <input type="text" class="form-control " id="complemento"
                                                name="complemento" maxlength="100" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">

                                        <div class="col-md-4">
                                            <label for="cidade" class="form-label">Cidade</label>
                                            <input type="text" class="form-control" id="cidade" name="cidade"
                                                maxlength="150" value="">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-control" id="estado" name="estado">
                                                <option value="0" selected>
                                                    Selecione</option>
                                                @foreach ($estados as $estado)
                                                    <option value="{{ $estado['id'] }}">{{ $estado['estado'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row mt-2">


                                    </div>
                                    <div class="row mt-2">

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status" checked>
                                            <label class="form-check-label" for="status">Ativo</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="dados_comerciais" class="row col-md-12 dados" style="display:none;">
                                    <div class="col-md-4">
                                        <label for="limite_credito" class="form-label">Nome</label>
                                        <input type="text" class="form-control mask_phone" id="telefone"
                                            name="telefone" maxlength="11" value="">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="limite_credito" class="form-label">Contato</label>
                                        <input type="text" class="form-control" id="contato" name="contato"
                                            value="">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="limite_credito" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="fechar_modal_alteracao"
                                    data-dismiss="modal">Fechar</button>
                                <button type="button" id="salvar_clientes_alterar"
                                    class="btn btn-primary">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
