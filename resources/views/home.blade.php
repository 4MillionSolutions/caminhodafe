@extends('adminlte::page')

@section('title', 'Membro da igreja')

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js"></script>
<script src="js/main_custom.js"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
    <div class="right_col" role="main">
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Bem-vindo</h1>
            <div class="col-sm-1">
                <img src="{{ asset('img/img_logo.png') }}" alt="Logo" class="img-fluid">
            </div>
        </div>



@stop
