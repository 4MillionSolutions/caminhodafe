/**
 * Melhorias de UX para formulários
 * - Loading durante submissão
 * - Confirmação de cancelamento
 * - Auto-dismiss de alertas
 */

$(document).ready(function() {
    // Salvar dados originais do formulário para detectar mudanças
    let formOriginalData = '';
    if ($('form').length) {
        formOriginalData = $('form').find('input, select, textarea').serialize();
    }
    
    // Loading no submit do formulário
    $('form').on('submit', function(e) {
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        
        // Desabilita o botão e mostra loading
        $submitBtn.prop('disabled', true);
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        // Se o formulário não validar, reabilita o botão
        if (!$form[0].checkValidity()) {
            setTimeout(function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }, 100);
        }
    });
    
    // Confirmação de cancelamento se houver alterações
    $('button[onclick*="history.back"]').on('click', function(e) {
        if ($('form').length) {
            const currentData = $('form').find('input, select, textarea').serialize();
            
            if (currentData !== formOriginalData && currentData !== '') {
                if (!confirm('Existem alterações não salvas. Deseja realmente cancelar?')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }
        }
    });
    
    // Auto-dismiss de alertas após 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Permitir fechar alerta manualmente
    $('.alert .close').on('click', function() {
        $(this).parent().fadeOut('fast', function() {
            $(this).remove();
        });
    });
});
