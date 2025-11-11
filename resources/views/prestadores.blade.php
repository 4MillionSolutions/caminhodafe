{{-- Bloco de pesquisa --}}
<div class="prestadores">
    <div class="right_col" role="main">
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse border mx-auto p-3" id="div_pesquisa">
                    <div class="form-group row">
                        <h6> Pesquisa</h6>
                        <form id="filtro" action="prestadores" method="get" data-parsley-validate=""
                            class="form-horizontal form-label-left" novalidate="">
                            <div class="container">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                    <table id='table_prestadores' class="table table-striped text-center">
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
    <div class="modal fade" id="modal_incluir"  data-acao="incluir"  tabindex="-1" role="dialog" aria-labelledby="modal_incluir"   aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <form method="POST" action="prestadores/incluir" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Incluir de prestador</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_incluir" href="#dados_prestadores_incluir">Dados do prestador</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#regioes_atendimentos_incluir">Regiões de atendimentos</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#dados_bancarios_incluir">Dados bancários</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#arquivos_incluir">Arquivos</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_prestadores_incluir" class="row col-md-12 dados" style="display:block;">
                                    @include('formularios.tiposPessoasFormulario')
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label for="nome" class="form-label">Nome</label>
                                            <input type="text" class="form-control " id="modal_nome"
                                                name="nome" maxlength="180" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="documento" class="form-label label_documento">CPF</label>
                                            <input type="text" class="form-control cpf" id="modal_documento"
                                                name="documento" maxlength="14" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="modal_telefone" class="form-label">Contato</label>
                                            <input type="text" class="form-control mask_phone" id="modal_telefone" name="telefone"
                                                value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="funcao" class="form-label">Função</label>
                                            <select class="form-control" id="modal_funcao" name="funcao">
                                                <option value="0" selected>
                                                    Selecione</option>
                                                @foreach ([['id' => 1, 'nome' => 'Engenheiro'], ['id' => 2, 'nome' => 'Arquiteto']] as $funcao)
                                                    <option value="{{ $funcao['id'] }}">{{ $funcao['nome'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-2">
                                            <label for="crea_cau" class="form-label">CREA/CAU</label>
                                            <input type="text" class="form-control" id="crea_cau" name="crea_cau"
                                                value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="limite_credito" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="modal_email" name="email"
                                                value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cep" class="form-label">CEP</label>
                                            <input type="text" class="form-control cep modal_cep" id="modal_cep"
                                                name="cep" maxlength="8" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="endereco" class="form-label">Endereço</label>
                                            <input type="text" class="form-control " id="modal_endereco"
                                                name="endereco" maxlength="500" value="">
                                        </div>
                                        <div class="col-md-1">
                                            <label for="numero" class="form-label">Número</label>
                                            <input type="text" class="form-control " id="modal_numero"
                                                name="numero" maxlength="20" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">

                                        <div class="col-md-3">
                                            <label for="bairro" class="form-label">Bairro</label>
                                            <input type="text" class="form-control " id="modal_bairro"
                                                name="bairro" maxlength="100" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="complemento" class="form-label">Complemento</label>
                                            <input type="text" class="form-control " id="modal_complemento"
                                                name="complemento" maxlength="100" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cidade" class="form-label">Cidade</label>
                                            <input type="text" class="form-control" id="modal_cidade" name="cidade"
                                                maxlength="150" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-control" id="modal_estado" name="estado">
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
                                        <div class="col-md-8">
                                            <label for="observacao" class="form-label">Observação</label>
                                            <textarea class="form-control" id="modal_observacoes" name="observacao" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="created_at" class="form-label">Criado em</label>
                                            <input class="form-control mask_date" type="text" disabled id="modal_created_at" name="created_at" value="">
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

                                <div id="regioes_atendimentos_incluir" class="row col-md-12 dados" style="display:none;">
                                    <div class="col-md-8">
                                        <label for="maps" class="form-label">Maps</label>
                                        <div id="div-maps-incluir"  style="width: 100%; height: 400px; border-radius: 10px;"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-control" id="modal_estado_regiao_incluir" name="estado_regiao">
                                            <option value="0" >Selecione</option>
                                            @foreach ($estados as $estado)
                                                <option data-sigla="{{ $estado['sigla'] }}" value="{{ $estado['id'] }}" >{{ $estado['estado'] }}</option>
                                            @endforeach
                                        </select>
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <select class="form-control" id="modal_cidades_regiao_incluir" name="cidades_regiao">
                                            <option value="0">Selecione</option>
                                        </select>

                                        <label for="raio" class="form-label">Raio (km)</label>
                                        <input type="number" class="form-control col-md-4" id="raio_incluir" name="raio" value="50" min="1" max="100">



                                        <label for="servico"  class="form-label">Serviço</label>
                                        <select class="form-control" multiple id="modal_servico_regiao_incluir" name="servico">
                                            @foreach ($servicos as $servico)
                                                <option value="{{ $servico['id'] }}">{{ $servico['id'] ."-". $servico['nome'] }}</option>
                                            @endforeach
                                        </select>

                                        <label for="valor"  class="form-label">Valor</label>
                                        <input type="text" class="form-control col-md-4 mask_valor " id="modal_valor_incluir" name="valor" value="">

                                        <div class="col-md-12 p-2 text-right">
                                            <button type="button" class="btn btn-success" id="adicionar_regiao">Adicionar</button>
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-striped " id='table_regioes'>
                                            <thead>
                                                <tr>
                                                    <th scope="col">UF</th>
                                                    <th scope="col">Cidades</th>
                                                    <th scope="col">Raio(km)</th>
                                                    <th scope="col">Serviços habilitados</th>
                                                    <th scope="col">Valor do serviço</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="dados_bancarios_incluir" class="row col-md-12 dados" style="display:none;">

                                    <div class="col-md-4">
                                        <label for="banco" class="form-label">Banco</label>
                                        <select class="form-control" id="modal_banco" name="banco">
                                            <option value="0" >Selecione</option>
                                            @foreach ($bancos as $banco)
                                                <option value="{{ $banco['id'] }}" >{{ $banco['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="agencia" class="form-label">Agencia</label>
                                        <input type="text" class="form-control" id="modal_agencia" name="agencia" value="">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="conta" class="form-label">Conta</label>
                                        <input type="text" class="form-control" id="modal_conta" name="conta" value="">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="conta" class="form-label">Tipo conta</label>
                                        <select class="form-control" id="modal_tipo" name="tipo">
                                            <option value="0" >Selecione</option>
                                            <option value="1" >Corrente</option>
                                            <option value="2" >Poupanca</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="tipo chave pix" class="form-label">Tipo chave pix</label>
                                        <select class="form-control" id="modal_tipo_chave_pix" name="tipo_chave_pix">
                                            <option value="0" >Selecione</option>
                                            <option value="1" >CPF</option>
                                            <option value="2" >CNPJ</option>
                                            <option value="3" >Email</option>
                                            <option value="4" >Aleatória</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="chave_pix" class="form-label">Chave Pix</label>
                                        <input type="text" class="form-control" id="modal_chave_pix" name="chave_pix" value="">
                                    </div>

                                </div>

                                <div id="arquivos_incluir" class="row col-md-12 dados" style="display:none;">

                                    @include('formularios.formularioArquivos')

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_incluir"
                            data-dismiss="modal">Fechar</button>
                        <button type="submit" id="salvar_prestadores_incluir"
                            class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <input type="hidden" class="form-control" id="modal_tabela_latitude" name="latitude" value="">
    <input type="hidden" class="form-control" id="modal_tabela_longitude" name="longitude" value="">

    {{-- MODAL ALTERAÇÃO --}}
    <div class="modal fade" id="modal_alteracao" data-acao="alterar" tabindex="-1" role="dialog" aria-labelledby="modal_alteracao" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <form method="POST" action="prestadores/alterar" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alteração de prestador</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="container">
                                <input type="hidden" id="modal_id" name="id" value="">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active modal_nave dados_alterar" href="#dados_prestadores">Dados do prestador</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#regioes_atendimentos">Regiões de atendimentos</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#dados_bancarios">Dados bancários</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link modal_nave" href="#arquivos">Arquivos</a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="dados_prestadores" class="row col-md-12 dados" style="display:block;">
                                    @include('formularios.tiposPessoasFormulario')
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label for="nome" class="form-label">Nome</label>
                                            <input type="text" class="form-control " id="modal_nome"
                                                name="nome" maxlength="180" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="documento" class="form-label label_documento">CNPJ</label>
                                            <input type="text" class="form-control cnpj" id="modal_documento"
                                                name="documento" maxlength="14" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="modal_telefone" class="form-label">Contato</label>
                                            <input type="text" class="form-control mask_phone" id="modal_telefone" name="telefone"
                                                value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="funcao" class="form-label">Função</label>
                                            <select class="form-control" id="modal_funcao" name="funcao">
                                                <option value="0" selected>
                                                    Selecione</option>
                                                @foreach ([['id' => 1, 'nome' => 'Engenheiro'], ['id' => 2, 'nome' => 'Arquiteto']] as $funcao)
                                                    <option value="{{ $funcao['id'] }}">{{ $funcao['nome'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-2">
                                            <label for="crea_cau" class="form-label">CREA/CAU</label>
                                            <input type="text" class="form-control" id="crea_cau" name="crea_cau"
                                                value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="modal_email" name="email"
                                                value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cep" class="form-label">CEP</label>
                                            <input type="text" class="form-control cep modal_cep" id="modal_cep"
                                                name="cep" maxlength="8" value="{{ $cientes[0]->cep ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="endereco" class="form-label">Endereço</label>
                                            <input type="text" class="form-control " id="modal_endereco"
                                                name="endereco" maxlength="500" value="">
                                        </div>
                                        <div class="col-md-1">
                                            <label for="numero" class="form-label">Número</label>
                                            <input type="text" class="form-control " id="modal_numero"
                                                name="numero" maxlength="20" value="">
                                        </div>
                                    </div>
                                    <div class="row mt-2">

                                        <div class="col-md-3">
                                            <label for="bairro" class="form-label">Bairro</label>
                                            <input type="text" class="form-control " id="modal_bairro"
                                                name="bairro" maxlength="100" value="">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="complemento" class="form-label">Complemento</label>
                                            <input type="text" class="form-control " id="modal_complemento"
                                                name="complemento" maxlength="100" value="">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cidade" class="form-label">Cidade</label>
                                            <input type="text" class="form-control" id="modal_cidade" name="cidade"
                                                maxlength="150" value="">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-control" id="modal_estado" name="estado">
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
                                        <div class="col-md-8">
                                            <label for="observacao" class="form-label">Observação</label>
                                            <textarea class="form-control" id="modal_observacoes" name="observacao" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="created_at" class="form-label">Criado em</label>
                                            <input class="form-control mask_date" type="text" disabled id="modal_created_at" name="created_at" value="">
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

                                <div id="regioes_atendimentos" class="row col-md-12 dados" style="display:none;">
                                    <div class="col-md-8">
                                        <label for="maps" class="form-label">Maps</label>
                                        <div id="div-maps-alterar" style="width: 100%; height: 400px; border-radius: 10px;"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-control" id="modal_estado_regiao_alterar" name="estado_regiao">
                                            <option value="0" >Selecione</option>
                                            @foreach ($estados as $estado)
                                                <option data-sigla="{{ $estado['sigla'] }}" value="{{ $estado['id'] }}" >{{ $estado['estado'] }}</option>
                                            @endforeach
                                        </select>
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <select class="form-control" id="modal_cidades_regiao_alterar" name="cidades_regiao">
                                            <option value="0">Selecione</option>
                                        </select>

                                        <label for="raio" class="form-label">Raio (km)</label>
                                        <input type="number" class="form-control col-md-4 raio" id="raio_alterar" name="raio" value="50" min="1" max="100">

                                        {{-- <label for="cidade_regiao"  class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade_regiao_alterar" name="cidade_regiao" value=""> --}}

                                        <label for="servico"  class="form-label">Serviço</label>
                                        <select class="form-control" multiple id="modal_servico_regiao_alterar" name="servico">
                                            @foreach ($servicos as $servico)
                                                <option value="{{ $servico['id'] }}">{{ $servico['id'] ."-". $servico['nome'] }}</option>
                                            @endforeach
                                        </select>

                                        <label for="valor"  class="form-label">Valor</label>
                                        <input type="text" class="form-control col-md-4 mask_valor " id="modal_valor_alterar" name="valor" value="">

                                        <div class="col-md-12 p-2 text-right">
                                            <button type="button" class="btn btn-success" id="adicionar_regiao">Adicionar</button>
                                        </div>

                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-striped " id='table_regioes'>
                                            <thead>
                                                <tr>
                                                    <th scope="col">UF</th>
                                                    <th scope="col">Cidades</th>
                                                    <th scope="col">Raio(km)</th>
                                                    <th scope="col">Serviços habilitados</th>
                                                    <th scope="col">Valor do serviço</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="dados_bancarios" class="row col-md-12 dados" style="display:none;">

                                    <div class="col-md-4">
                                        <label for="banco" class="form-label">Banco</label>
                                        <select class="form-control" id="modal_banco" name="banco">
                                            <option value="0" >Selecione</option>
                                            @foreach ($bancos as $banco)
                                                <option value="{{ $banco['id'] }}" >{{ $banco['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="agencia" class="form-label">Agencia</label>
                                        <input type="text" class="form-control" id="modal_agencia" name="agencia" value="">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="conta" class="form-label">Conta</label>
                                        <input type="text" class="form-control" id="modal_conta" name="conta" value="">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="conta" class="form-label">Tipo conta</label>
                                        <select class="form-control" id="modal_tipo" name="tipo">
                                            <option value="0" >Selecione</option>
                                            <option value="1" >Corrente</option>
                                            <option value="2" >Poupanca</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="tipo chave pix" class="form-label">Tipo chave pix</label>
                                        <select class="form-control" id="modal_tipo_chave_pix" name="tipo_chave_pix">
                                            <option value="0" >Selecione</option>
                                            <option value="1" >CPF</option>
                                            <option value="2" >CNPJ</option>
                                            <option value="3" >Email</option>
                                            <option value="4" >Aleatória</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="chave_pix" class="form-label">Chave Pix</label>
                                        <input type="text" class="form-control" id="modal_chave_pix" name="chave_pix" value="">
                                    </div>
                                </div>


                                <div id="arquivos" class="row col-md-12 dados" style="display:none;">

                                    @include('formularios.formularioArquivos')

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12">
                        <button type="button" class="btn btn-secondary" id="fechar_modal_alteracao"
                            data-dismiss="modal">Fechar</button>
                        <button type="submit" id="salvar_prestadores_alterar"
                            class="btn btn-primary btn_desabilita_editar">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
