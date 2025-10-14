@extends('adminlte::page')

@section('title', 'Controle de Laudos')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <img src="{{ asset('img/img_logo.png') }}" alt="PROJEART" class="img-fluid" style="height: 40px;">
                </div>
                <div class="text-center">
                    <h2 class="mb-0">CONTROLE DE ENTREGA DE LAUDOS</h2>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filtros
                        </button>
                        <div class="dropdown-menu">
                            <form class="p-3" method="GET" action="{{ route('controle-laudos.index') }}">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control" name="cliente" value="{{ request('cliente') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">Todos</option>
                                        <option value="Enviado para o cliente" {{ request('status') == 'Enviado para o cliente' ? 'selected' : '' }}>Enviado para o cliente</option>
                                        <option value="Enviado para análise" {{ request('status') == 'Enviado para análise' ? 'selected' : '' }}>Enviado para análise</option>
                                        <option value="Laudo em lançamento" {{ request('status') == 'Laudo em lançamento' ? 'selected' : '' }}>Laudo em lançamento</option>
                                        <option value="Falta vistoria" {{ request('status') == 'Falta vistoria' ? 'selected' : '' }}>Falta vistoria</option>
                                        <option value="Laudo em confecção" {{ request('status') == 'Laudo em confecção' ? 'selected' : '' }}>Laudo em confecção</option>
                                        <option value="Falta responsável do laudo" {{ request('status') == 'Falta responsável do laudo' ? 'selected' : '' }}>Falta responsável do laudo</option>
                                        <option value="Remarcar Vistoria" {{ request('status') == 'Remarcar Vistoria' ? 'selected' : '' }}>Remarcar Vistoria</option>
                                        <option value="Falta estagiário" {{ request('status') == 'Falta estagiário' ? 'selected' : '' }}>Falta estagiário</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Data Vistoria</label>
                                    <input type="date" class="form-control" name="data_vistoria" value="{{ request('data_vistoria') }}">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                                    <a href="{{ route('controle-laudos.index') }}" class="btn btn-outline-secondary btn-sm">Limpar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <a href="{{ route('controle-laudos.index') }}" class="btn btn-outline-info">Ir Banco de Dados</a>
                    <button class="btn btn-outline-success" onclick="exportar()">Enviar Me</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabela Principal de Laudos -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Laudos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Proposta</th>
                                    <th>Cetip</th>
                                    <th>Hor Homo</th>
                                    <th>Hor Foto</th>
                                    <th>Tipo</th>
                                    <th>Endereço</th>
                                    <th>Bairro</th>
                                    <th>Cidade</th>
                                    <th>Estado</th>
                                    <th>Data Vistoria</th>
                                    <th>Hora Vistoria</th>
                                    <th>Resp. Vistoria</th>
                                    <th>CREA/CAU</th>
                                    <th>Resp. Laudo</th>
                                    <th>Status</th>
                                    <th>Hora Envio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laudos as $laudo)
                                <tr class="{{ $loop->iteration % 2 == 0 ? 'table-light' : '' }}">
                                    <td>{{ $laudo->cliente }}</td>
                                    <td>{{ $laudo->proposta }}</td>
                                    <td>{{ $laudo->cetip }}</td>
                                    <td>{{ $laudo->hor_homo ? $laudo->hor_homo->format('H:i:s') : '' }}</td>
                                    <td>{{ $laudo->hor_foto ? $laudo->hor_foto->format('H:i:s') : '' }}</td>
                                    <td>{{ $laudo->tipo }}</td>
                                    <td class="text-truncate" style="max-width: 150px;" title="{{ $laudo->endereco }}">
                                        {{ $laudo->endereco }}
                                    </td>
                                    <td>{{ $laudo->bairro }}</td>
                                    <td>{{ $laudo->cidade }}</td>
                                    <td>{{ $laudo->estado }}</td>
                                    <td>{{ $laudo->data_vistoria ? $laudo->data_vistoria->format('d/m/Y') : '' }}</td>
                                    <td>{{ $laudo->hora_vistoria ? $laudo->hora_vistoria->format('H:i:s') : '' }}</td>
                                    <td>{{ $laudo->resp_vistoria }}</td>
                                    <td>{{ $laudo->crea_cau }}</td>
                                    <td>{{ $laudo->resp_laudo }}</td>
                                    <td>
                                        @php
                                            $status = $laudo->status_calculado;
                                            $isError = in_array($status, ['Falta responsável do laudo!', 'Remarcar Vistoria!', 'Enviado para análise!', 'Falta estagiário!', 'Falta vistoria!', 'Laudo em confecção!', 'Laudo em lançamento!']);
                                        @endphp
                                        <span class="badge {{ $isError ? 'bg-danger' : 'bg-success' }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>{{ $laudo->hora_envio ? $laudo->hora_envio->format('H:i') : '' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="17" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Nenhum laudo encontrado</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            @if($laudos->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $laudos->links() }}
            </div>
            @endif
        </div>

        <!-- Painel Lateral -->
        <div class="col-lg-4">
            <!-- Tabela de Condomínios -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Condomínio/Área</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Condomínio</th>
                                    <th>Área Terreno</th>
                                    <th>Área Privativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laudos->take(10) as $laudo)
                                    @foreach($laudo->condominios as $condominio)
                                    <tr>
                                        <td class="text-truncate" style="max-width: 120px;" title="{{ $condominio->nome }}">
                                            {{ $condominio->nome ?: 'Isolado' }}
                                        </td>
                                        <td>{{ $condominio->area_terreno ? number_format($condominio->area_terreno, 2, ',', '.') . ' m²' : '-' }}</td>
                                        <td>{{ $condominio->area_privativa ? number_format($condominio->area_privativa, 2, ',', '.') . ' m²' : '-' }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                @if($laudos->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Nenhum condomínio</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>{{ now()->format('d/m') }}</small>
                </div>
            </div>

            <!-- Análise Diária -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">ANÁLISE DIÁRIA</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="text-center">
                                <strong>Cetip</strong>
                                <div class="form-control-plaintext">{{ $analiseDiaria['cetip_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <strong>Interno</strong>
                                <div class="form-control-plaintext">{{ $analiseDiaria['interno_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <strong>Externo</strong>
                                <div class="form-control-plaintext">{{ $analiseDiaria['externo_count'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="text-center">
                                <strong>Qnt. laudos</strong>
                                <div class="form-control-plaintext">{{ $analiseDiaria['total_laudos'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>- ESTAGIÁRIOS:</strong>
                        @foreach($analiseDiaria['estagiarios'] as $estagiario)
                        <div class="d-flex justify-content-between">
                            <span>{{ $estagiario['nome'] }}</span>
                            <span>{{ $estagiario['quantidade'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <strong>- ENGENHEIROS E ARQUITETOS:</strong>
                        @foreach($analiseDiaria['engenheiros'] as $engenheiro)
                        <div class="d-flex justify-content-between">
                            <span>{{ $engenheiro['nome'] }}</span>
                            <span>{{ $engenheiro['quantidade'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary btn-sm w-100" onclick="trazerDados()">Trazer dados</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportar() {
    // Implementar exportação
    alert('Funcionalidade de exportação será implementada');
}

function trazerDados() {
    // Recarregar a página com dados atualizados
    window.location.reload();
}
</script>
@endsection

@section('adminlte_js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/bootstrap.4.6.2.js"></script>
    <script src="js/main_custom.js"></script>
@stop
