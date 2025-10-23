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
        var payload = {};
        $(modalSelector).find('[id^="modal_"]').each(function () {
            var id = $(this).attr('id'); // e.g. modal_nome, modal_crea_cau
            if (!id) return;
            var key = id.replace(/^modal_/, '');
            payload[key] = $(this).val();
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
            var tableId = '#table_' + resource;
            if ($(tableId).length && $.fn.dataTable) {
                $(tableId).DataTable().ajax.reload(null, false);
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
                var tableId = '#table_' + resource;
                if ($(tableId).length && $.fn.dataTable) {
                    $(tableId).DataTable().ajax.reload(null, false);
                }
            },
            error: function (jqXHR) {
                alert('Erro ao processar requisição: ' + (jqXHR.responseText || jqXHR.statusText));
            }
        });

    });

    $(document).on('click', '#adicionar_regiao', function() {
        $estado = $('#estado').val();
        $cidade = $('#cidade_regiao').val();
        $raio = $('#raio').val();
        $servico = $('#servico').val();
        $valor = $('#valor').val();

        $('#table_regioes tbody').append('<tr><td>'+ $estado +'</td><td>'+ $cidade +'</td><td>'+ $raio +'</td><td>'+ $servico +'</td><td>'+ $valor +'</td></tr>').




        $('#servico').val('');
        $('#valor').val('');
    })

}); // fecha function($)
