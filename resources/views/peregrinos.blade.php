@extends('adminlte::page')

@section('title', 'CRM')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
        @if($perfil == 1)
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @endif
    @stop
    @section('content')
    <div class="right_col" role="main">

        @if($perfil == 1)
            <form id="filtro" action="clientes" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label">Nome</label>
                    <div class="col-sm-4">
                        <input type="hide" id="nome" name="nome" class="form-control" value="@if (isset($request) && $request->input('nome') != ''){{$request->input('nome')}}@else @endif">
                    </div>

                    <label for="status" class="col-sm-1 col-form-label"></label>
                    <select class="form-control col-md-1" id="status" name="status">
                        <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                        <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
                    </select>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                    <div class="col-sm-5">
                    </div>
                </div>
            </form>
        @endif
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
                      <th>Telefone</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($peregrinos))
                        @foreach ($peregrinos as $peregrino)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $peregrino->id )) }}>{{$peregrino->id}}</a></th>
                              <td>{{$peregrino->nome}}</td>
                              <td class='mask_phone'>{{$peregrino->telefone}}</td>
                              <td>{{$peregrino->email}}</td>
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

    @stop
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left"  method="post">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($peregrinos[0]->id)){{$peregrinos[0]->id}}@else{{''}}@endif">
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                <div class="col-sm-4">
                <input type="text" class="form-control is-invalid" required id="nome"  name="nome" value="@if (isset($peregrinos[0]->nome)){{$peregrinos[0]->nome}}@else{{''}}@endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="endereco" class="col-sm-2 col-form-label">Endereço</label>
                <div class="col-sm-7">
                <input type="text" class="form-control" id="endereco" name="endereco" value="@if (isset($peregrinos[0]->endereco)){{$peregrinos[0]->endereco}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="numero" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-1">
                <input type="text" class="form-control sonumeros" id="numero" name="numero" value="@if (isset($peregrinos[0]->numero)){{$peregrinos[0]->numero}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="bairro" class="col-sm-2 col-form-label">Bairro</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" id="bairro" name="bairro" value="@if (isset($peregrinos[0]->bairro)){{$peregrinos[0]->bairro}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="cidade" class="col-sm-2 col-form-label">Cidade</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="cidade" name="cidade" value="@if (isset($peregrinos[0]->cidade)){{$peregrinos[0]->cidade}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="estado" name="estado" value="@if (isset($peregrinos[0]->estado)){{$peregrinos[0]->estado}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="cep" class="col-sm-2 col-form-label">Cep</label>
                <div class="col-sm-2">
                <input type="text" class="form-control cep" id="cep" name="cep" value="@if (isset($peregrinos[0]->cep)){{$peregrinos[0]->cep}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="telefone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-2">
                <input type="text" class="form-control mask_phone" id="telefone" name="telefone" value="@if (isset($peregrinos[0]->telefone)){{$peregrinos[0]->telefone}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                <input type="text" class="form-control is-invalid" required id="email" name="email" value="@if (isset($peregrinos[0]->email)){{$peregrinos[0]->email}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="senha" class="col-sm-2 col-form-label">Senha</label>
                <div class="col-sm-4">
                <input type="text" class="form-control is-invalid" id="senha" name="senha" value="">
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($peregrinos[0]->status) && $peregrinos[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($peregrinos[0]->status) && $peregrinos[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    @stop
@endif
@section('js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/acoes.js"></script>
@stop
