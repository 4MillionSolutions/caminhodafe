/**
 * Agendamentos FASE 1 - JavaScript Principal
 * Responsável por toda a lógica de interação e AJAX
 */

const AgendamentosApp = {
    config: {
        csrfToken: $('meta[name="csrf-token"]').attr('content'),
        endpoints: {
            consultarCep: '/agendamentos/consultar-cep',
            cadastrarImovel: '/agendamentos/cadastrar-imovel',
            prestadoresRecomendados: '/agendamentos/prestadores-recomendados',
            salvarAgendamento: '/agendamentos/salva',
            atribuirPrestador: '/agendamentos/atribuir-prestador',
            enviarProducao: '/agendamentos/enviar-producao',
            reagendar: '/agendamentos/reagendar',
            retorno: '/agendamentos/retorno',
            reavaliacao: '/agendamentos/reavaliacao',
            getAuditoria: '/agendamentos/auditoria',
            ajaxAgendamentos: '/ajax/agendamentos'
        },
        elementos: {
            btnNovoAgendamento: '.btn-abrir-novo-agendamento',
            modalNovoAgendamento: '#modal_novo_agendamento',
            modalVisualizarAgendamento: '#modal_visualizar_agendamento',
            formCadastroImovel: '#form_cadastro_imovel',
            formDadosAgendamento: '#form_dados_agendamento',
            inputCep: '#input_cep',
            btnConsultarCep: '#btn_consultar_cep',
            btnProximoPrestador: '#btn_proximo_prestador',
            btnVoltarImovel: '#btn_voltar_imovel',
            btnProximoDados: '#btn_proximo_dados',
            btnVoltarPrestador: '#btn_voltar_prestador',
            btnSalvarAgendamento: '#btn_salvar_agendamento_novo',
            containerPrestadores: '#container_prestadores',
            tabelaAgendamentos: '#table_agendamentos'
        }
    },

    /**
     * Inicializar a aplicação
     */
    init: function() {
        this.setupMascaras();
        this.inicializarDataTable();
        this.setupEventListeners();
        console.log('AgendamentosApp inicializado com sucesso');
    },

    /**
     * Configurar máscaras de entrada
     */
    setupMascaras: function() {
        if ($.fn.mask) {
            $('#input_cep').mask('00000-000');
            $('#input_numero_contato').mask('(00) 00000-0000');
        }
    },

    /**
     * Inicializar DataTable
     */
    inicializarDataTable: function() {
        const self = this;
        
        $(this.config.elementos.tabelaAgendamentos).DataTable({
            ajax: {
                url: this.config.endpoints.ajaxAgendamentos,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'numero_sequencial' },
                { data: 'cliente' },
                { data: 'imovel' },
                { data: 'endereco' },
                { data: 'data' },
                { data: 'hora_inicio' },
                { data: 'prestador' },
                { data: 'ativo' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-info" onclick="AgendamentosApp.visualizarAgendamento(${row.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="AgendamentosApp.deletarAgendamento(${row.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    },
                    orderable: false
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json'
            },
            autoWidth: false,
            pageLength: 10,
            searching: true,
            ordering: true,
            paging: true,
            destroy: true
        });
    },

    /**
     * Configurar event listeners
     */
    setupEventListeners: function() {
        const self = this;

        // Abrir novo agendamento
        $(document).on('click', this.config.elementos.btnNovoAgendamento, function() {
            $(self.config.elementos.modalNovoAgendamento).modal('show');
            self.limparFormularios();
        });

        // Consultar ViaCEP
        $(document).on('click', this.config.elementos.btnConsultarCep, function() {
            self.consultarViaCep();
        });

        // Próximo: Prestador
        $(document).on('click', this.config.elementos.btnProximoPrestador, function() {
            if (self.validarFormularioImovel()) {
                self.carregarPrestadoresRecomendados();
            }
        });

        // Voltar de Prestador para Imóvel
        $(document).on('click', this.config.elementos.btnVoltarImovel, function() {
            $('#tab_imovel').tab('show');
        });

        // Próximo: Dados
        $(document).on('click', this.config.elementos.btnProximoDados, function() {
            $('#tab_dados').tab('show');
        });

        // Voltar de Dados para Prestador
        $(document).on('click', this.config.elementos.btnVoltarPrestador, function() {
            $('#tab_prestador').tab('show');
        });

        // Salvar agendamento
        $(document).on('click', this.config.elementos.btnSalvarAgendamento, function() {
            self.salvarNovoAgendamento();
        });

        // Enter no campo CEP
        $(document).on('keypress', this.config.elementos.inputCep, function(e) {
            if (e.which == 13) {
                self.consultarViaCep();
                return false;
            }
        });
    },

    /**
     * Consultar CEP no ViaCEP
     */
    consultarViaCep: function() {
        const cep = $('#input_cep').val().replace(/\D/g, '');
        
        if (cep.length !== 8) {
            this.mostrarAlerta('CEP inválido. Por favor, verifique', 'warning');
            return;
        }

        const spinner = $('#spinner_cep');
        spinner.addClass('show');

        $.ajax({
            url: this.config.endpoints.consultarCep,
            type: 'POST',
            data: {
                cep: cep,
                _token: this.config.csrfToken
            },
            success: (response) => {
                if (response.success) {
                    this.preencherDadosViaCep(response.data);
                    this.mostrarAlerta('CEP consultado com sucesso!', 'success');
                } else {
                    this.mostrarAlerta(response.message, 'warning');
                }
            },
            error: () => {
                this.mostrarAlerta('Erro ao consultar CEP', 'danger');
            },
            complete: () => {
                spinner.removeClass('show');
            }
        });
    },

    /**
     * Preencher dados do ViaCEP
     */
    preencherDadosViaCep: function(dados) {
        $('#input_logradouro').val(dados.logradouro || '');
        $('#input_complemento_viacep').val(dados.complemento || '');
        $('#input_bairro').val(dados.bairro || '');
        $('#input_cidade').val(dados.cidade || '');
        $('#input_estado').val(dados.estado || '');
    },

    /**
     * Validar formulário de imóvel
     */
    validarFormularioImovel: function() {
        const campos = {
            'Cliente': $('#input_cliente_id').val(),
            'CEP': $('#input_cep').val(),
            'Número': $('#input_numero').val(),
            'Tipo': $('#input_tipo_enum').val()
        };

        for (const [nome, valor] of Object.entries(campos)) {
            if (!valor) {
                this.mostrarAlerta(`Campo obrigatório: ${nome}`, 'warning');
                return false;
            }
        }

        return true;
    },

    /**
     * Carregar prestadores recomendados
     */
    carregarPrestadoresRecomendados: function() {
        const self = this;
        const formData = new FormData($(this.config.elementos.formCadastroImovel)[0]);
        const dados = Object.fromEntries(formData);
        dados._token = this.config.csrfToken;

        // Salvar imóvel primeiro
        $.ajax({
            url: this.config.endpoints.cadastrarImovel,
            type: 'POST',
            data: dados,
            success: function(response) {
                if (response.success) {
                    $('#input_imovel_id').val(response.data.id);
                    self.carregarPrestadores(response.data.id);
                    $('#tab_prestador').tab('show');
                    self.mostrarAlerta('Imóvel cadastrado com sucesso!', 'success');
                } else {
                    self.mostrarAlerta('Erro: ' + response.message, 'danger');
                }
            },
            error: function() {
                self.mostrarAlerta('Erro ao cadastrar imóvel', 'danger');
            }
        });
    },

    /**
     * Carregar prestadores do servidor
     */
    carregarPrestadores: function(imovelId) {
        const self = this;

        $.ajax({
            url: this.config.endpoints.prestadoresRecomendados,
            type: 'GET',
            data: {
                imovel_id: imovelId
            },
            success: function(response) {
                if (response.success) {
                    self.exibirPrestadores(response.data);
                } else {
                    $(self.config.elementos.containerPrestadores).html(
                        '<div class="alert alert-warning">Nenhum prestador encontrado para esta localização</div>'
                    );
                }
            },
            error: function() {
                self.mostrarAlerta('Erro ao carregar prestadores', 'danger');
            }
        });
    },

    /**
     * Exibir prestadores na interface
     */
    exibirPrestadores: function(prestadores) {
        let html = '';

        if (prestadores.length === 0) {
            html = '<div class="alert alert-warning">Nenhum prestador disponível para esta localização</div>';
        } else {
            prestadores.forEach((p) => {
                const whatsapp = p.whatsapp ? `<p class="mb-0"><small>💬 ${p.whatsapp}</small></p>` : '';
                html += `
                    <div class="prestador-card" onclick="AgendamentosApp.selecionarPrestador(${p.id}, '${p.nome}', event)">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-2">${p.nome}</h6>
                                <p class="mb-1"><small class="text-muted">📍 ${p.localizacao}</small></p>
                                <p class="mb-0"><small class="text-muted">⭐ Avaliação: ${p.avaliacao || 'N/A'}</small></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <h5 class="text-success mb-2">R$ ${p.valor_hora || 'N/A'}/h</h5>
                                <p class="mb-0"><small>📱 ${p.telefone}</small></p>
                                ${whatsapp}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        $(this.config.elementos.containerPrestadores).html(html);
    },

    /**
     * Selecionar prestador
     */
    selecionarPrestador: function(prestadorId, prestadorNome, event) {
        $('#input_prestador_id').val(prestadorId);

        // Remove seleção anterior
        $('.prestador-card').removeClass('selected');

        // Adiciona seleção atual
        $(event.currentTarget).addClass('selected');

        // Habilita botão próximo
        $('#btn_proximo_dados').prop('disabled', false);
    },

    /**
     * Salvar novo agendamento
     */
    salvarNovoAgendamento: function() {
        const self = this;
        
        const dados = {
            cliente_id: $('#input_cliente_id').val(),
            imovel_id: $('#input_imovel_id').val(),
            prestador_id: $('#input_prestador_id').val(),
            contato_nome: $('#input_contato_nome').val(),
            numero_contato_formatted: $('#input_numero_contato').val(),
            data: $('#input_data_agendamento').val(),
            hora_inicio: $('#input_hora_inicio').val(),
            hora_fim: $('#input_hora_fim').val() || null,
            data_criacao_demanda: $('#input_data_criacao_demanda').val() || null,
            data_vencimento_sla: $('#input_data_vencimento_sla').val() || null,
            os_plataforma: $('#input_os_plataforma').val() || null,
            observacao_externa: $('#input_observacao_externa').val() || null,
            _token: this.config.csrfToken
        };

        // Validações básicas
        if (!dados.cliente_id || !dados.imovel_id || !dados.prestador_id) {
            this.mostrarAlerta('Por favor, complete todas as etapas', 'warning');
            return;
        }

        $.ajax({
            url: this.config.endpoints.salvarAgendamento,
            type: 'POST',
            data: dados,
            success: function(response) {
                if (response.success) {
                    self.mostrarAlerta('Agendamento salvo com sucesso!', 'success');
                    $(self.config.elementos.modalNovoAgendamento).modal('hide');
                    $(self.config.elementos.tabelaAgendamentos).DataTable().ajax.reload();
                } else {
                    self.mostrarAlerta('Erro: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                self.mostrarAlerta('Erro ao salvar agendamento', 'danger');
            }
        });
    },

    /**
     * Visualizar agendamento
     */
    visualizarAgendamento: function(agendamentoId) {
        const self = this;

        $.ajax({
            url: `/agendamentos/${agendamentoId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    self.preencherModalVisualizacao(response.data);
                    self.carregarAuditoria(agendamentoId);
                    $(self.config.elementos.modalVisualizarAgendamento).modal('show');
                }
            },
            error: function() {
                self.mostrarAlerta('Erro ao carregar agendamento', 'danger');
            }
        });
    },

    /**
     * Preencher modal de visualização
     */
    preencherModalVisualizacao: function(agendamento) {
        $('#view_agendamento_id').val(agendamento.id);
        $('#view_os_interna').text(agendamento.os_interna || 'N/A');
        $('#view_os_plataforma').text(agendamento.os_plataforma || 'N/A');
        $('#view_status_badge').text(agendamento.status || 'RASCUNHO').attr('class', `badge badge-${this.normalizarStatus(agendamento.status || 'RASCUNHO')}`);
        $('#view_cliente_nome').text(agendamento.cliente?.nome || agendamento.cliente?.nome_empresa || 'N/A');
        $('#view_endereco_completo').text(agendamento.imovel?.endereco || 'N/A');
        $('#view_data').text(this.formatarData(agendamento.data));
        $('#view_hora_inicio').text(agendamento.hora_inicio || 'N/A');
        $('#view_prestador_nome').text(agendamento.prestador?.nome || 'N/A');
        $('#view_contato_nome').text(agendamento.contato_nome || 'N/A');
        $('#view_numero_contato').text(agendamento.numero_contato_formatted || 'N/A');
        $('#view_observacao_externa').text(agendamento.observacao_externa || 'Sem observações');

        // Mostrar/Ocultar botões de ação baseado no status
        this.configurarBotoesAcao(agendamento.status);
    },

    /**
     * Carregar auditoria
     */
    carregarAuditoria: function(agendamentoId) {
        const self = this;

        $.ajax({
            url: this.config.endpoints.getAuditoria + '/' + agendamentoId,
            type: 'GET',
            data: {
                agendamento_id: agendamentoId
            },
            success: function(response) {
                if (response.success) {
                    self.exibirAuditoria(response.data);
                }
            }
        });
    },

    /**
     * Exibir auditoria
     */
    exibirAuditoria: function(auditorias) {
        let html = '';

        if (auditorias.length === 0) {
            html = '<div class="alert alert-info">Sem histórico de auditoria</div>';
        } else {
            auditorias.forEach((item) => {
                const acaoClass = this.getAuditoriaClass(item.acao);
                const mudancas = item.campo_alterado ? 
                    `<div class="audit-changes"><strong>${item.campo_alterado}:</strong> ${item.valor_anterior || 'N/A'} → ${item.valor_novo || 'N/A'}</div>` : '';

                html += `
                    <div class="audit-item ${acaoClass}">
                        <div class="audit-header">
                            <span class="audit-action">${this.traduzirAcao(item.acao)}</span>
                            <span class="audit-time">${item.data_acao}</span>
                        </div>
                        <p class="audit-user">Por: <strong>${item.usuario}</strong></p>
                        ${mudancas}
                    </div>
                `;
            });
        }

        $('#audit_timeline').html(html);
    },

    /**
     * Deletar agendamento
     */
    deletarAgendamento: function(agendamentoId) {
        if (!confirm('Tem certeza que deseja deletar este agendamento?')) {
            return;
        }

        const self = this;

        $.ajax({
            url: '/agendamentos/deletar',
            type: 'POST',
            data: {
                id: agendamentoId,
                _token: this.config.csrfToken
            },
            success: function(response) {
                if (response.success) {
                    self.mostrarAlerta('Agendamento deletado com sucesso!', 'success');
                    $(self.config.elementos.tabelaAgendamentos).DataTable().ajax.reload();
                }
            },
            error: function() {
                self.mostrarAlerta('Erro ao deletar agendamento', 'danger');
            }
        });
    },

    /**
     * Limpar formulários
     */
    limparFormularios: function() {
        $(this.config.elementos.formCadastroImovel)[0].reset();
        $(this.config.elementos.formDadosAgendamento)[0].reset();
        $('#input_prestador_id').val('');
        $('#input_imovel_id').val('');
        $('#btn_proximo_dados').prop('disabled', true);
        $('.prestador-card').removeClass('selected');
        $('#tab_imovel').tab('show');
    },

    /**
     * Mostrar alerta
     */
    mostrarAlerta: function(mensagem, tipo = 'info') {
        // Pode ser customizado conforme o design do projeto
        alert(mensagem);
        // Ou usar Toast/Notification library como toastr ou sweetalert
    },

    /**
     * Utilitários
     */
    formatarData: function(data) {
        if (!data) return 'N/A';
        const d = new Date(data);
        return d.toLocaleDateString('pt-BR');
    },

    normalizarStatus: function(status) {
        const mapa = {
            'RASCUNHO': 'rascunho',
            'ATRIBUIDO': 'atribuido',
            'PRODUCAO': 'producao',
            'CONCLUIDO': 'concluido',
            'REAGENDADO': 'reagendado',
            'RETORNO': 'retorno',
            'REAVALIACAO': 'reavaliacao',
            'CANCELADO': 'cancelado'
        };
        return mapa[status] || 'rascunho';
    },

    traduzirAcao: function(acao) {
        const mapa = {
            'CRIADO': '✨ Criado',
            'ATRIBUIDO': '👤 Atribuído a Prestador',
            'STATUS_ALTERADO': '📊 Status Alterado',
            'ENVIADO_PRODUCAO': '📤 Enviado para Produção',
            'REAGENDADO': '📅 Reagendado',
            'CRIADO_RETORNO': '🔄 Retorno Criado',
            'CRIADO_REAVALIACAO': '🔍 Reavaliação Criada',
            'OBSERVACAO_ADICIONAL': '📝 Observação Adicionada'
        };
        return mapa[acao] || acao;
    },

    getAuditoriaClass: function(acao) {
        const mapa = {
            'CRIADO': 'action-criado',
            'ATRIBUIDO': 'action-atribuido',
            'ENVIADO_PRODUCAO': 'action-producao',
            'REAGENDADO': 'action-reagendado',
            'CRIADO_RETORNO': 'action-retorno',
            'CRIADO_REAVALIACAO': 'action-reavaliacao'
        };
        return mapa[acao] || '';
    },

    configurarBotoesAcao: function(status) {
        // Ocultar todos os botões
        $('#btn_reagendar, #btn_retorno, #btn_reavaliacao, #btn_atribuir_prestador, #btn_enviar_producao').hide();

        // Mostrar baseado no status
        switch(status) {
            case 'RASCUNHO':
                $('#btn_atribuir_prestador').show();
                break;
            case 'ATRIBUIDO':
                $('#btn_reagendar').show();
                $('#btn_enviar_producao').show();
                break;
            case 'PRODUCAO':
                $('#btn_reagendar').show();
                break;
            case 'CONCLUIDO':
                $('#btn_retorno').show();
                $('#btn_reavaliacao').show();
                break;
        }
    }
};

// Inicializar quando o DOM estiver pronto
$(document).ready(function() {
    AgendamentosApp.init();
});
