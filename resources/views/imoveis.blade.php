
{{-- Bloco de pesquisa --}}
<div class="imoveis">
    <div class="right_col" role="main">
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                    <div class="form-group row">
                        <h6> Pesquisa</h6>
                        <form id="filtro" action="imoveis" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                            <div class="container">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="nome" class="col-sm-1 col-form-label">Nome</label>
                                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ativo" class="col-sm-1 col-form-label">Situação</label>
                                        <select class="form-control" id="ativo" name="ativo">
                                            <option value="A" {{ $request->input('ativo') == 'A' ? 'selected' : '' }}>Ativo</option>
                                            <option value="I" {{ $request->input('ativo') == 'I' ? 'selected' : '' }}>Inativo</option>
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
                <table id='table_imoveis' class="table table-striped text-center">
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
<div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" aria-labelledby="modal_incluir" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="imoveis/incluir">
            @csrf
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inclusão de imóvel</h5>
            </div>
            <div class="modal-body">
                <div class="form-group row">

                    <div class="container">

                        <div class="row row-cols-md-3 g-3 mt-2">

                            <div class="col-md-8">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control " id="endereco" name="endereco" maxlength="500" value="{{ $imoveis[0]->endereco ?? '' }}">
                            </div>
                            <div class="col-md-1">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control " id="numero" name="numero" maxlength="20" value="{{ $imoveis[0]->numero ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control " id="complemento" name="complemento" maxlength="100" value="{{ $imoveis[0]->complemento ?? '' }}">
                            </div>
                        </div>
                        <div class="row row-cols-md-3 g-3 mt-2">
                            <div class="col-md-4">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control " id="bairro" name="bairro" maxlength="100" value="{{ $imoveis[0]->bairro ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" maxlength="150" value="{{ $imoveis[0]->cidade ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="0" {{ isset($imoveis[0]->estado) && $imoveis[0]->estado == '' ? 'selected' : '' }}>Selecione</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado['id'] }}" {{ isset($imoveis[0]->estado) && $imoveis[0]->estado == $estado['id'] ? 'selected' : '' }}>{{ $estado['estado'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row row-cols-md-3 g-3 mt-2">

                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control cep" id="cep" name="cep" maxlength="8" value="{{ $imoveis[0]->cep ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="contato" class="form-label">Contato</label>
                                <input type="text" class="form-control" id="contato" name="contato" value="{{ $imoveis[0]->contato ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control mask_phone" id="telefone" name="telefone" maxlength="11" value="{{ $imoveis[0]->telefone ?? '' }}">
                            </div>

                        </div>
                        <div class="row row-cols-md-3 g-3">

                            <div class="col-md-12">
                                <label for="imovel" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes">{{ $imoveis[0]->observacoes ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="fechar_modal_incluir" data-dismiss="modal">Fechar</button>
                <button type="button" id="salvar_imoveis_incluir" class="btn btn-primary">Confirmar</button>
            </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ALTERAÇÃO --}}
<div class="modal fade" id="modal_alteracao" tabindex="-1" role="dialog" aria-labelledby="modal_alteracao" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <form method="POST" action="imoveis/alterar">
            @csrf
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alteração de imóvel</h5>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                        <div class="container">

                        <div class="row row-cols-md-3 g-3 mt-2">

                            <div class="col-md-8">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control " id="endereco" name="endereco" maxlength="500" value="{{ $imoveis[0]->endereco ?? '' }}">
                            </div>
                            <div class="col-md-1">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control " id="numero" name="numero" maxlength="20" value="{{ $imoveis[0]->numero ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control " id="complemento" name="complemento" maxlength="100" value="{{ $imoveis[0]->complemento ?? '' }}">
                            </div>
                        </div>
                        <div class="row row-cols-md-3 g-3 mt-2">
                            <div class="col-md-4">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control " id="bairro" name="bairro" maxlength="100" value="{{ $imoveis[0]->bairro ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" maxlength="150" value="{{ $imoveis[0]->cidade ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="0" {{ isset($imoveis[0]->estado) && $imoveis[0]->estado == '' ? 'selected' : '' }}>Selecione</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado['id'] }}" {{ isset($imoveis[0]->estado) && $imoveis[0]->estado == $estado['id'] ? 'selected' : '' }}>{{ $estado['estado'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row row-cols-md-3 g-3 mt-2">

                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control cep" id="cep" name="cep" maxlength="8" value="{{ $imoveis[0]->cep ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <label for="contato" class="form-label">Contato</label>
                                <input type="text" class="form-control" id="contato" name="contato" value="{{ $imoveis[0]->contato ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control mask_phone" id="telefone" name="telefone" maxlength="11" value="{{ $imoveis[0]->telefone ?? '' }}">
                            </div>

                        </div>
                        <div class="row row-cols-md-3 g-3">

                            <div class="col-md-12">
                                <label for="imovel" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes">{{ $imoveis[0]->observacoes ?? '' }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="ativo" class="form-label">Situação</label>
                                <select class="form-control" id="modal_ativo" name="ativo">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="fechar_modal_alteracao" data-dismiss="modal">Fechar</button>
                <button type="button" id="salvar_imoveis_alterar" class="btn btn-primary">Confirmar</button>
            </div>
            </div>
        </form>
    </div>
</div>
