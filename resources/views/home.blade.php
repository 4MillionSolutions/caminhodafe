@extends('adminlte::page')

@section('title', 'CRM Projearte')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">

        <div class="col-md-4">
            <label for="dias_viagem" class="form-label">Dias de peregrinação</label>
            <input type="number" class="form-control calcular" id="dias_viagem" name="dias_viagem"
                value="10">
        </div>
        <div class="col-md-4">
            <label for="gastros_extras" class="form-label">Gastos extras</label>
            <select class="form-control calcular" id="gastros_extras" name="gastros_extras">
                <option value="0" >Sem gastos extras</option>
                <option value="30" >R$ 30/dia (mínimo essencial) </option>
                <option value="50" >R$ 50/dia (média realista para a maioria dos peregrinos) </option>
                <option value="80" >R$ 80/dia (cenário confortável, considerando possíveis extras)</option>
            </select>
        </div>
        <div class="col-md-4">
                <img src="img/img_logo.png" alt="" style="width: 110px; height:110px" class="img-fluid">
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3 id='total_menor_valor'>R$ 200,00<sup style="font-size: 20px"></sup></h3>

                <p>Menor Valor de hospedagem</p>
              </div>
              <div class="icon">
                <i class="fa fa-bed"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id='total_maior_valor'>R$ 220,00<sup style="font-size: 20px"></sup></h3>

                <p>Maior Valor de hospedagem</p>
              </div>
              <div class="icon">
                <i class="fa fa-bed"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id='total_menor_valor_com_desconto'>R$ 190,00<sup style="font-size: 20px"></sup></h3>

                <p>Menor Valor com desconto</p>
              </div>
              <div class="icon">
                <i class="fa fa-gift"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                 <p>Valor total de peregrinação</p>
                <h3 id='total_com_desconto'>R$ 190,00<sup style="font-size: 20px"></sup></h3>
              </div>
              <div class="icon">
                <i class="fa fa-star"></i>
              </div>
            </div>
          </div>


    </div>
    @stop
    @section('content')
    @extends('layouts.extra-content')
    <div class="right_col" role="main">
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table id='table_hospedagens' class="table table-striped text-center">
                  <thead>
                    <tr>
                      <th title="Selecione as hospedagens">Selecione</th>
                      <th>Pousada</th>
                      <th>Cidade</th>
                      <th>Menor Valor</th>
                      <th>Maior Valor</th>
                      <th>Valor Café</th>
                      <th>Desconto Parceiro</th>
                      <th>Email</th>
                      <th>Telefone</th>
                      <th>Telefone 2</th>
                      <th>Observações</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($hospedagens))
                        @foreach ($hospedagens as $hospedagenl)
                            <tr>
                              <th scope="row">
                                    @if($hospedagenl->desconto_parceiro > '0.00')
                                        <i  class="fa fa-tag text-warning fa-2x ml-3" title="Contém Desconto de {{number_format($hospedagenl->desconto_parceiro, 2, ',','')}}% com nosso cupom do Entre passos e Propósitos" aria-hidden="true"></i>
                                    @else
                                        <i  class=" text-warning fa-2x ml-5" aria-hidden="true"></i>
                                    @endif
                                    <input type="checkbox" name="id[]" class="form-check-input ml-3 calcular" value="{{$hospedagenl->id}}">

                                </th>
                                <td>{{$hospedagenl->nome}}</td>
                                <td>{{$cidades[$hospedagenl->cidade]}}</td>
                              <td class="menor_valor">{{number_format($hospedagenl->menor_valor, 2, ',','')}}</td>
                              <td class="maior_valor">{{number_format(empty($hospedagenl->maior_valor) ? $hospedagenl->menor_valor : $hospedagenl->maior_valor, 2, ',','')}}</td>
                              <td class="valor_cafe">{{number_format($hospedagenl->valor_cafe, 2, ',','')}}</td>
                              <td class="desconto_parceiro">@if($hospedagenl->desconto_parceiro > '0.00') {{number_format($hospedagenl->desconto_parceiro, 2, ',','')}}% <i class="fa fa-tag text-warning fa-2x" aria-hidden="true"></i> @endif </td>
                              <td>{{$hospedagenl->email}}</td>
                              <td>{{$hospedagenl->telefone}}</td>
                              <td>{{$hospedagenl->telefone2}}</td>
                              <td>{{$hospedagenl->observacoes}}</td>
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
@endif

@section('js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    {{-- <script src="js/bootstrap.4.6.2.js"></script> --}}
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/acoes.js"></script>
@stop
