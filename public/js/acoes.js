$(function ($) {


    $(document).on('click', '.modal_nave', function (e) {
        e.preventDefault();

        $('.modal_nave').removeClass('active');
        $(this).addClass('active');
        $('.dados').hide();

        var $link = $(this);
        var target = $link.attr('href');
        $(target).show();
    });

    $(document).on('click', '.acao_abrir_modal_incluir', function (e) {

        $('.dados_incluir').click();

    })

    function getCsrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function fetchResource(resource, id, cb) {
        $.ajax({
            type: "GET",
            url: '/cadastros/' + resource + '/' + id,
            data: {
                id: id,
                ajax: true,
                _token: getCsrf(),
            },
            success: function (data) {
                cb(null, data);
            },
            error: function (jqXHR) {
                cb(jqXHR);
            }
        });
    }

    function collectModalData(modalSelector) {

        let modalSelectorAtivo = $(".modal.show"); // modal visível

        let payload = {};

        // ✅ Campos do modal (ids)
        modalSelectorAtivo.find('[id^="modal_"]').each(function () {
            let id = $(this).attr('id');
            let key = id.replace(/^modal_/, '');

            //se tipo for um radio ou checkbox, pega o value do input checked
            if ($(this).attr('type') === 'radio' || $(this).attr('type') === 'checkbox') {
                if ($(this).is(':checked')) {
                    payload[key] = $(this).val();
                }
            } else {
                payload[key] = $(this).val();
            }



        });

        // ✅ Campos da tabela dentro do modal (names)
        modalSelectorAtivo.find('[name^="modal_tabela_"]').each(function () {
            let name = $(this).attr('name'); // ex: modal_tabela_cidades[]
            let key = name.replace(/^modal_tabela_/, '').replace(/\[\]$/, '');

            if (!payload[key]) payload[key] = [];
            payload[key].push($(this).val());
        });


        payload._token = getCsrf();
        return payload;
    }

    function submitResource(resource, action, payload, idForUrl, done, fail) {
        var url = '/cadastros/' + resource + '/' + action + (idForUrl ? ('/' + idForUrl) : '');
        $.ajax({
            type: "POST",
            url: url,
            data: payload,
            success: function (data) {
                if (typeof done === 'function') done(data);
            },
            error: function (jqXHR) {
                if (typeof fail === 'function') fail(jqXHR);
            }
        });
    }

    // generic "alterar" button click (opens modal and fills fields)
    $(document).on('click', '[class*="alterar_"]', function (e) {
        e.preventDefault();
        var $btn = $(this);

        // find class like alterar_tecnicos or alterar_clientes
        var cls = ($btn.attr('class') || '').split(/\s+/).find(function (c) {
            return c.indexOf('alterar_') === 0;
        });
        if (!cls) return;
        var resource = cls.replace('alterar_', '');

        var id = $btn.data('id');
        if (!id) return;


        fetchResource(resource, id, function (err, data) {
            if (err) {
                alert('Erro ao obter dados: ' + (err.responseText || err.statusText));
                return;
            }
            // expected shape: data[resource][0]
            var obj = (data[resource] && data[resource][0]) || {};
            // populate modal_alteracao fields that exist
            Object.keys(obj).forEach(function (key) {
                var $el = $('#modal_alteracao #modal_' + key);
                if ($el.length) $el.val(obj[key]);
            });

            $('#modal_alteracao #table_regioes tbody').empty();
            $('#modal_alteracao #table_regioes tbody').append(obj.tabela_regioes);

            $('.dados_alterar').trigger('click');

            setTimeout(function() {


                $('#modal_alteracao .sonumeros').unmask();
                $('#modal_alteracao .mask_minutos').unmask();
                $('#modal_alteracao .mask_horas').unmask();
                $('#modal_alteracao .mask_valor').unmask();
                $('#modal_alteracao .mask_date').unmask();
                $('#modal_alteracao .mask_data_hora').unmask();
                $('#modal_alteracao .cpf').unmask();
                $('#modal_alteracao .cnpj').unmask();
                $('#modal_alteracao .cep').unmask();
                $('.mask_phone').unmask();
                $('#modal_alteracao .cep').mask('00000-000');
                $('#modal_alteracao .sonumeros').mask('000000000000');
                $('#modal_alteracao .mask_minutos').mask('00:00');
                $('#modal_alteracao .mask_horas').mask('00:00:00');
                $('#modal_alteracao .mask_valor').mask("000.000.000,00");
                $('#modal_alteracao .mask_date').mask('00/00/0000');
                $('#modal_alteracao .mask_data_hora').mask('00/00/0000 00:00:00');

                var stipo_pessoa = $('#modal_alteracao .tipo_pessoa:checked').val();
                if(stipo_pessoa === 'F') {
                    $('.label_documento').text('CPF');
                    $('#modal_alteracao #modal_documento').removeClass('cnpj').addClass('cpf');
                    $('#modal_alteracao #modal_documento').unmask();
                    $('#modal_alteracao #modal_documento').mask('000.000.000-00');

                    $('#modal_incluir #modal_documento').removeClass('cnpj').addClass('cpf');
                    $('#modal_incluir #modal_documento').unmask();
                    $('#modal_incluir #modal_documento').mask('000.000.000-00');
                } else {
                    $('.label_documento').text('CNPJ');
                    $('#modal_alteracao #modal_documento').removeClass('cpf').addClass('cnpj');
                    $('#modal_alteracao #modal_documento').unmask();
                    $('#modal_alteracao #modal_documento').mask('00.000.000/0000-00');
                    $('#modal_incluir #modal_documento').removeClass('cpf').addClass('cnpj');
                    $('#modal_incluir #modal_documento').unmask();
                    $('#modal_incluir #modal_documento').mask('00.000.000/0000-00');
                }



                var behavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },

                options = {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(behavior.apply({}, arguments), options);
                    }
                };
                $('#modal_alteracao  .mask_phone').mask(behavior, options);
            }, 500);


        });
    });

    // generic salvar handler for ids like salvar_tecnicos_alterar or salvar_clientes_incluir
    $(document).on('click', '[id^="salvar_"]', function (e) {
        e.preventDefault();
        var parts = this.id.split('_'); // ["salvar", "tecnicos", "alterar"]
        if (parts.length < 3) return;
        var resource = parts[1];
        var action = parts[2]; // 'alterar' or 'incluir'
        var modalSelector = (action === 'alterar') ? '#modal_alteracao' : '#modal_incluir';
        var payload = collectModalData(modalSelector);
        var idForUrl = (action === 'alterar') ? payload.id || payload.modal_id || payload['id'] : null;
        // ensure if modal had modal_id it's used as id param
        if (payload.modal_id && !payload.id) {
            payload.id = payload.modal_id;
            delete payload.modal_id;
        }

        submitResource(resource, action, payload, idForUrl, function () {
            var tableInstance = window['table_' + resource];
            if (tableInstance && tableInstance.ajax) {
                tableInstance.ajax.reload(null, false);
                alert('Registro salvo com sucesso!');
            }
            // close modal
            var fechar = (action === 'alterar') ? '#fechar_modal_alteracao' : '#fechar_modal_incluir';
            $(fechar).click();
        }, function (jqXHR) {
            alert('Erro ao processar requisição: ' + (jqXHR.responseText || jqXHR.statusText));
        });
    });


    //função generica para deletar
    $(document).on('click', '[id="excluir"]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        if (!confirm('Confirma a exclusão deste registro?')) {
            return;
        }

        //pega o id da Table onde o botão está localizado
        var $table = $btn.closest('table');
        if (!$table.length) return;
        var tableId = $table.attr('id');
        var resource = tableId.replace('table_', '');

        var id = $btn.data('id');

        if (!id) return;

        $.ajax({
            type: "POST",
            url: '/cadastros/' + resource + '/excluir/' + id,
            data: {
                id: id,
                ajax: true,
                _token: getCsrf(),
            },
            success: function () {
                var tableInstance = window['table_' + resource];
                if (tableInstance && tableInstance.ajax) {
                    tableInstance.ajax.reload(null, false);
                    alert('Registro excluido!');
                }
            },
            error: function (jqXHR) {
                alert('Erro ao processar requisição: ' + (jqXHR.responseText || jqXHR.statusText));
            }
        });

    });

    $(document).on('click', '#adicionar_regiao', function() {

        let modalId = $(".modal.show").attr("id"); // pega o modal visível
        let acao = $(".modal.show").data("acao");
        let estado = $('#' + modalId + ' #modal_estado_regiao_' + acao).find(':selected').text();
        let estado_codigo = $('#' + modalId + ' #modal_estado_regiao_' + acao).find(':selected').val();
        let longitude = $('#' + modalId + ' #modal_tabela_longitude').val();
        let latitude = $('#' + modalId + ' #modal_tabela_latitude').val();
        let cidade = $('#' + modalId + ' #cidade_regiao_' + acao).val();

        let raio = $('#' + modalId + ' #raio_' + acao).val();
        let valor = $('#' + modalId + ' #modal_valor_' + acao).val();
        let servicos = $('#' + modalId + ' #modal_servico_regiao_' + acao).find(':selected').map(function () {
            return $(this).text(); // nomes dos serviços
        }).get();

        let servico_codigos = $('#' + modalId + ' #modal_servico_regiao_' + acao).val(); // ids dos serviços

        let campos_servico = [];

        servico_codigos.forEach(function (codigo, i) {
            let nome = servicos[i];
            campos_servico.push(`<span title="${nome}">${codigo}</span>`);
        });

        campos_servico = campos_servico.join(', ');

        $('#' + modalId + ' #table_regioes tbody').append('<tr><td>'+
            estado +' <input type="hidden" name="modal_tabela_stados[]" value="'+ estado_codigo +'"></td><td>'+
            cidade +' <input type="hidden" name="modal_tabela_cidades[]" value="'+ cidade +'"><input type="hidden" name="modal_tabela_latitude[]" value="'+ latitude +'"><input type="hidden" name="modal_tabela_longitude[]" value="'+ longitude +'"></td><td>'+
            raio +' <input type="hidden" name="modal_tabela_raios[]" value="'+ raio +'"></td><td>'+
            campos_servico +' <input type="hidden" name="modal_tabela_servicos[]" value="'+ servico_codigos +'"></td><td>'+
            valor +' <input type="hidden" name="modal_tabela_valores[]" value="'+ valor +'"></td></tr>');

        $('#' + modalId + ' #servico').val('');
        $('#' + modalId + ' #valor').val('');
    })


}); // fecha function($)
