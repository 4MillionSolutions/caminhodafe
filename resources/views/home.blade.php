@extends('adminlte::page')
@section('layout-top-nav', false)
@section('title', 'CRM Projearte')

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js"></script>
<script src="js/main_custom.js"></script>
    {{-- <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
    <div class="text-center" role="main">

        <h1 class="m-0 text-dark text-left col-sm-2 col-form-label">Bem-vindo</h1>
        <div class="col-sm-12 text-center">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid  ">
        </div>

    </div>



@stop
