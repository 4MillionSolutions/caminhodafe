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
    <script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
    <script src="js/jquery.mask.js?cache={{time()}}"></script>
    <script src="js/main_custom.js?cache={{time()}}"></script>
    {{-- <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}

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
        <form id="filtro" action="clientes" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label text-right">Nome</label>
                <div class="col-sm-3">
                    <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}">
                </div>
                <label for="documento" class="col-sm-1 col-form-label text-right">Documento</label>
                <div class="col-sm-2">
                    <input type="text" id="documento" name="documento" class="form-control mask_cpf_cnpj" value="{{ $request->input('documento') ?? '' }}">
                </div>
                <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                <div class="col-sm-2">
                    <select class="form-control" id="ativo" name="ativo">
                        <option value="A" {{ $request->input('ativo') == 'A' ? 'selected' : '' }}>Ativo</option>
                        <option value="I" {{ $request->input('ativo') == 'I' ? 'selected' : '' }}>Inativo</option>
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
                      <th>Telefone</th>
                      <th>Nascimento</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($clientes))
                        @foreach ($clientes as $cliente)
                            <tr>
                            <th scope="row" title="Editar cliente"><a href={{ URL::route($rotaAlterar, array('id' => $cliente->id )) }}>{{$cliente->id}}</a></th>
                              <td>{{$cliente->name}}</td>
                              <td class='mask_phone'>{{$cliente->telefone}}</td>
                              <td>{{$cliente->email}}</td>
                              <td>{{\Carbon\Carbon::parse($cliente->data_nascimento)->format('d/m/Y')}}</td>
                              <td title="Ir para atendimentos"><a href={{ URL::route('alterar-clientes', array('id' => $cliente->id )) }}><i class="fa fa-clipboard"></i></a></td>
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
            <input type="hidden" name="id" value="{{ $clientes[0]->id ?? '' }}">
        @endif
        <div class="container">
            <div class="row row-cols-md-3 g-3">
                <div class="col-md-4">
                    <label for="cliente" class="form-label">Nome*</label>
                    <input type="text" class="form-control" id="nome" name="nome" maxlength="200" required value="{{ $clientes[0]->nome ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="documento" class="form-label">Documento*</label>
                    <input type="text" class="form-control" id="documento" name="documento" maxlength="14" value="{{ $clientes[0]->documento ?? '' }}" >
                </div>
                <div class="col-md-4">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control mask_phone" id="telefone" name="telefone" maxlength="11" value="{{ $clientes[0]->telefone ?? '' }}">
                </div>
            </div>
            <div class="row row-cols-md-3 g-3 mt-2">

                <div class="col-md-8">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control " id="endereco" name="endereco" maxlength="500" value="{{ $clientes[0]->endereco ?? '' }}">
                </div>
                <div class="col-md-1">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control " id="numero" name="numero" maxlength="20" value="{{ $clientes[0]->numero ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control " id="complemento" name="complemento" maxlength="100" value="{{ $clientes[0]->complemento ?? '' }}">
                </div>
            </div>
            <div class="row row-cols-md-3 g-3 mt-2">
                <div class="col-md-4">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control " id="bairro" name="bairro" maxlength="100" value="{{ $clientes[0]->bairro ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" maxlength="150" value="{{ $clientes[0]->cidade ?? '' }}">
                </div>
            </div>
            <div class="row row-cols-md-3 g-3 mt-2">
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="0" {{ isset($clientes[0]->estado) && $clientes[0]->estado == '' ? 'selected' : '' }}>Selecione</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado['id'] }}" {{ isset($clientes[0]->estado) && $clientes[0]->estado == $estado['id'] ? 'selected' : '' }}>{{ $estado['estado'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control cep" id="cep" name="cep" maxlength="8" value="{{ $clientes[0]->cep ?? '' }}">
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-2">
                    <label for="ativo" class="form-label">Situação</label>
                    <select class="form-control" id="ativo" name="ativo">
                        <option value="0" {{ isset($cliente[0]->ativo) && $cliente[0]->ativo == '0' ? 'selected' : '' }}>Inativo</option>
                        <option value="1" {{ isset($cliente[0]->ativo) && $cliente[0]->ativo == '1' ? 'selected' : '' }}>Ativo</option>
                    </select>
                </div>
            </div>
            <div class="row mt-4 text-center">
                <div class="col-md-12">
                    <button class="btn btn-danger mx-2" onclick="window.history.back();" type="button">Cancelar</button>
                    <button type="submit" class="btn btn-primary mx-2">Salvar</button>
                </div>
            </div>
        </div>
    </form>
@endif
@stop

@section('scripts')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="{{ asset('js/validarAno.js') }}"></script>
@endsection
