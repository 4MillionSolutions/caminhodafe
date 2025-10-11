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
        <form id="filtro" action="servicos" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
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
                      <th>Ativo</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($servicos))
                        @foreach ($servicos as $servico)
                            <tr>
                            <th scope="row" title="Editar serviço"><a href={{ URL::route($rotaAlterar, array('id' => $servico->id )) }}>{{$servico->id}}</a></th>
                              <td>{{$servico->nome}}</td>
                              <td>{{$servico->ativo == 'A' ? 'Ativo' : 'Inativo'}}</td>

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
            <input type="hidden" name="id" value="{{ $servicos[0]->id ?? '' }}">
        @endif
        <div class="container">
            <div class="row row-cols-md-3 g-3">
                <div class="col-md-4">
                    <label for="pessoa" class="form-label">Nome*</label>
                    <input type="text" class="form-control" id="nome" name="nome" maxlength="200" required value="{{ $servicos[0]->nome ?? '' }}">
                </div>
            </div>


            <div class="row mt-1">
                <div class="col-md-2">
                    <label for="ativo" class="form-label">Situação</label>
                    <select class="form-control" id="ativo" name="ativo">
                        <option value="0" {{ isset($servicos[0]->ativo) && $servicos[0]->ativo == '0' ? 'selected' : '' }}>Inativo</option>
                        <option value="1" {{ isset($servicos[0]->ativo) && $servicos[0]->ativo == '1' ? 'selected' : '' }}>Ativo</option>
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
@endsection
