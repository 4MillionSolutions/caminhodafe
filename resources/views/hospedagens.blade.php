@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')
    @if(isset($tela) and $tela == 'pesquisa')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
            <div class="col-sm-1">
                @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
            </div>
        </div>
    @endif

@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
@extends('layouts.extra-content')
@if(isset($tela) and $tela == 'pesquisa')
    <div class="right_col" role="main">
        <form id="filtro" action="hospedagens" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label text-right">Nome</label>
                <div class="col-sm-3">
                    <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}">
                </div>
                <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                <div class="col-sm-2">
                    <select class="form-control" id="ativo" name="ativo">
                        <option value="0" {{ $request->input('ativo') == '0' ? 'selected' : '' }}>Ativo</option>
                        <option value="1" {{ $request->input('ativo') == '1' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
                <div class="col-sm-5">
                </div>
            </div>
        </form>
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h4>Encontrados</h4>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="table table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nome</th>
                      <th>Situação</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($hospedagens))
                        @foreach ($hospedagens as $hospedagens)
                            <tr>
                            <th scope="row" title="Editar "><a href={{ URL::route($rotaAlterar, array('id' => $hospedagens->id )) }}>{{$hospedagens->id}}</a></th>
                              <td>{{$hospedagens->nome}}</td>
                              <td>{{$hospedagens->ativo == '1' ? 'Ativo' : 'Inativo'}}</td>

                            </tr>
                        @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>
@else
    <h1 class="m-0 text-dark">{{ $tela == 'alterar' ? 'Alteração de' : 'Inclusão de' }} {{ $nome_tela }}</h1>


    <form id="{{ $tela }}" action="{{ $tela == 'alterar' ? $rotaAlterar : $rotaIncluir }}" method="post">
        @csrf
        @if($tela == 'alterar')
            <input type="text" name="id" value="{{ $hospedagens->id ?? '' }}">
        @endif
        <div class="container">

            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <select class="form-control" id="cidade" name="cidade">
                        <option value="">Selecione</option>
                        @if(isset($cidades))
                            @foreach ($cidades as $key => $cidade)
                                <option value="{{ $key }}" {{ (isset($hospedagens->cidade) && $hospedagens->cidade == $key) ? 'selected' : '' }}>{{ $cidade }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>


                <div class="col-md-4">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" maxlength="50"
                        value="{{ $hospedagens->nome ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label for="menor_valor" class="form-label">Menor Valor</label>
                    <input type="text" class="form-control mask_valor" id="menor_valor" name="menor_valor"
                        value="{{ $hospedagens->menor_valor ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label for="maior_valor" class="form-label">Maior Valor</label>
                    <input type="text"  class="form-control mask_valor" id="maior_valor" name="maior_valor"
                        value="{{ $hospedagens->maior_valor ?? '' }}">
                </div>

            </div>

            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-2">
                    <label for="valor_cafe" class="form-label">Valor Café</label>
                    <input type="text"  class="form-control mask_valor" id="valor_cafe" name="valor_cafe"
                        value="{{ $hospedagens->valor_cafe ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label for="desconto_parceiro" class="form-label">Desconto Parceiro(%)</label>
                    <input type="text"  class="form-control mask_valor" id="desconto_parceiro" name="desconto_parceiro"
                        value="{{ $hospedagens->desconto_parceiro ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label for="parceiro" class="form-label">Parceiro</label>
                    <select class="form-control" id="parceiro" name="parceiro">
                        <option value="0" {{ (isset($hospedagens->parceiro) && !$hospedagens->parceiro) ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ (isset($hospedagens->parceiro) && $hospedagens->parceiro) ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>

            </div>

            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" maxlength="80"
                        value="{{ $hospedagens->email ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control mask_phone" id="telefone" name="telefone" maxlength="11"
                        value="{{ $hospedagens->telefone ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label for="telefone2" class="form-label">Telefone 2</label>
                    <input type="text" class="form-control mask_phone" id="telefone2" name="telefone2" maxlength="11"
                        value="{{ $hospedagens->telefone2 ?? '' }}">
                </div>

            </div>

            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" maxlength="50">{{ $hospedagens->observacoes ?? '' }}</textarea>
                </div>

            </div>

            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-1">
                    <label for="ativo" class="form-label">Ativo</label>
                    <select class="form-control" id="ativo" name="ativo">
                        <option value="1" {{ (isset($hospedagens->ativo) && $hospedagens->ativo) ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ (isset($hospedagens->ativo) && !$hospedagens->ativo) ? 'selected' : '' }}>Não</option>
                    </select>
                </div>

            </div>
            <div class="row row-cols-md-3 g-3 mt-2">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>


    </form>
@endif
@stop

@section('js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    {{-- <script src="js/bootstrap.4.6.2.js"></script> --}}
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/acoes.js"></script>
@stop
