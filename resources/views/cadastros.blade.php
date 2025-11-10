@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')
    @if(isset($tela) and $tela == 'pesquisa')
        <div class="form-group row">
            <h6 class="text-dark col-sm-8 col-form-label">Cadastros</h6>
            {{-- <div class="col-sm-1">
                @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
            </div> --}}
            <div class="col-sm-4">
                <p>
                    <button class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#div_pesquisa" aria-expanded="false" aria-controls="div_pesquisa">Pesquisar</button>
                    <button class="btn btn-outline-success acao_abrir_modal_incluir" type="button" data-toggle="modal" data-target="#modal_incluir" aria-expanded="false" aria-controls="modal_incluir">Adicionar</button>
                </p>
            </div>
        </div>
    @endif
@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link  rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')

    @php
        $aba = $request->route('aba') ?? 'clientes';
    @endphp
    <div class="right_col" role="main">
        <div class="form-group row">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $aba == 'clientes' ? 'active' : '' }}" href="/cadastros/clientes">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $aba == 'prestadores' ? 'active' : '' }}" href="/cadastros/prestadores">Prestadores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $aba == 'servicos' ? 'active' : '' }}" href="/cadastros/servicos">Serviços</a>
                </li>
            </ul>
            </form>
        </div>
    </div>


    @include($aba)

@stop

@section('js')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/main_custom.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/acoes.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/dataTableAcoes.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/maps.js') }}?v={{ time() }}"></script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxrTRxwlRJODloZQ47_FOXBKnByI9s5p4&callback=initMap">
    </script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
