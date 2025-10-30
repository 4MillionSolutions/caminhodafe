@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('content_header')

@stop

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kanban.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content')
@extends('layouts.extra-content')

    <div class="right_col" role="main">
        <div class="form-group">
            <div class="container-fluid mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold">Produção</h4>
                    <select class="form-select w-auto">
                        <option>Filtro: Responsável</option>
                    </select>
                </div>

                <div class="kanban-board">
                    <!-- Pendente -->
                    <div class="kanban-column" id="pendente">
                        <h5>Pendente</h5>
                        <div class="kanban-card">
                            <strong>OP-001</strong><br>
                            Cliente A<br>
                            Imóvel 1<br>
                            <span class="badge bg-warning text-dark">Alta</span>
                            <small class="text-muted d-block mt-1">08/03/2024</small>
                        </div>
                        <div class="kanban-card">
                            <strong>OP-003</strong><br>
                            Cliente C<br>
                            Imóvel 3<br>
                            <span class="badge bg-warning text-dark">Alta</span>
                            <small class="text-muted d-block mt-1">08/03/2024</small>
                        </div>
                    </div>

                    <!-- Em andamento -->
                    <div class="kanban-column" id="andamento">
                        <h5>Em andamento</h5>
                        <div class="kanban-card">
                            <strong>OP-002</strong><br>
                            Cliente B<br>
                            Imóvel 2<br>
                            <span class="badge bg-info text-dark">Média</span>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-info" style="width: 50%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Revisão -->
                    <div class="kanban-column" id="revisao">
                        <h5>Revisão</h5>
                        <div class="kanban-card">
                            <strong>OP-005</strong><br>
                            Cliente E<br>
                            Imóvel 5<br>
                            <span class="badge bg-warning text-dark">Alta</span>
                            <small class="text-muted d-block mt-1">12/03/2024</small>
                        </div>
                    </div>

                    <!-- Concluído -->
                    <div class="kanban-column" id="concluido">
                        <h5>Concluído</h5>
                        <div class="kanban-card">
                            <strong>OP-007</strong><br>
                            Cliente G<br>
                            Imóvel 7<br>
                            <span class="badge bg-success">Concluído</span>
                            <small class="text-muted d-block mt-1">07/03/2024</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL --}}
            <div class="modal fade" id="modal_detalhes" tabindex="-1" aria-labelledby="modal_detalhes_label" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title" id="modal_detalhes_label">OS-001</h5>
                                <div class="modal-subtitle">Cliente A • Imóvel X</div>
                            </div>
                            <span class="badge bg-primary status-badge">Em andamento</span>
                        </div>

                        <div class="modal-body">
                            <!-- Abas -->
                            <ul class="nav nav-tabs" id="tabsDetalhes" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="agendamento-tab" data-bs-toggle="tab" data-bs-target="#agendamento"
                                        type="button" role="tab">Agendamento</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="materiais-tab" data-bs-toggle="tab" data-bs-target="#materiais"
                                        type="button" role="tab">Materiais de Campo</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="obs-tab" data-bs-toggle="tab" data-bs-target="#observacoes"
                                        type="button" role="tab">Observações</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="tabsDetalhesContent">
                                <!-- TAB 1 -->
                                <div class="tab-pane fade show active" id="agendamento" role="tabpanel">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p><i class="bi bi-calendar-event"></i> <strong>24/04/2024</strong></p>
                                            <p><i class="bi bi-person-badge"></i> Técnico 2</p>
                                            <p><i class="bi bi-geo-alt"></i> Rua Exemplo, 123</p>
                                            <button class="reagendar-btn">Reagendar</button>

                                            <ul class="custom-checkbox-list mt-3">
                                                <li><i class="bi bi-check-circle-fill"></i> Matrícula</li>
                                                <li><i class="bi bi-check-circle-fill"></i> IPTU</li>
                                                <li><i class="bi bi-check-circle-fill"></i> ART</li>
                                                <li><i class="bi bi-circle"></i> Fotos</li>
                                            </ul>

                                            <div class="file-upload-area">
                                                Arraste os arquivos aqui ou clique para enviar
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="progress-box">
                                                <label class="form-label fw-bold">Progresso</label>
                                                <textarea class="form-control mb-2" rows="3" placeholder="Comentários internos"></textarea>

                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Progresso</small>
                                                    <small>60%</small>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: 60%;"></div>
                                                </div>

                                                <ul class="custom-checkbox-list mt-3">
                                                    <li><i class="bi bi-check-circle-fill"></i> Vistoria realizada</li>
                                                    <li><i class="bi bi-check-circle-fill"></i> Fotos enviadas</li>
                                                    <li><i class="bi bi-circle"></i> Laudo em revisão</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2 -->
                                <div class="tab-pane fade" id="materiais" role="tabpanel">
                                    <p class="mt-3 text-muted">Lista de materiais utilizados no campo...</p>
                                </div>

                                <!-- TAB 3 -->
                                <div class="tab-pane fade" id="observacoes" role="tabpanel">
                                    <textarea class="form-control mt-3" rows="5" placeholder="Digite observações adicionais..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button class="btn btn-primary">Salvar alterações</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>

@stop

@section('js')
    <script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
    <script src="js/jquery.mask.js?cache={{time()}}"></script>
    <script src="js/main_custom.js?cache={{time()}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js?cache={{time()}}"></script>
@endsection
