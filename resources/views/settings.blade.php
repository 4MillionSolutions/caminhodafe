@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')

        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Conta</h1>
        </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/bootstrap.4.6.2.js"></script>
    <script src="../js/main_custom.js"></script>
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
        <div class="right_col" role="main">
            <form action="alterar-senha" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id ?? '' }}">
                <div class="container">

                    <div class="row row-cols-md-3 g-3">
                        <div class="col-md-4">
                            <label for="pessoa" class="form-label">Nome*</label>
                            <input type="text" class="form-control" id="nome" name="nome" maxlength="200" required value="{{ $user->name ?? '' }}">
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="A" {{ isset($user->status) && $user->status == 'A' ? 'selected' : '' }}>Ativo</option>
                                <option value="I" {{ isset($user->status) && $user->status == 'I' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" readonly name="email" value='{{ $user->email }}' placeholder="Email">
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <label for="senha">Senha</label>
                            <input type="password" class="form-control" id="senha" name="password" value='' placeholder="Senha">
                        </div>
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
        </div>
    </div>
    @stop
