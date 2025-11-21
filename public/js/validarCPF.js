/**
 * Validação de CPF
 * Valida CPF tanto formatado quanto apenas números
 */

function validarCPF(cpf) {
    // Remove caracteres não numéricos
    cpf = cpf.replace(/[^\d]+/g, '');

    // Verifica se tem 11 dígitos
    if (cpf.length !== 11) return false;

    // Verifica se todos os dígitos são iguais (ex: 111.111.111-11)
    if (/^(\d)\1{10}$/.test(cpf)) return false;

    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    let digito1 = resto >= 10 ? 0 : resto;

    if (digito1 !== parseInt(cpf.charAt(9))) return false;

    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    let digito2 = resto >= 10 ? 0 : resto;

    if (digito2 !== parseInt(cpf.charAt(10))) return false;

    return true;
}

// Integração com o formulário quando o DOM estiver pronto
$(document).ready(function() {
    // Validação de CPF no blur (quando o campo perde o foco)
    $('#documento').on('blur', function() {
        const cpf = $(this).val();
        const $errorMsg = $('#cpf-error');

        if (cpf && !validarCPF(cpf)) {
            $errorMsg.show();
            $(this).addClass('is-invalid');
        } else {
            $errorMsg.hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Prevenir submit se CPF for inválido
    $('form').on('submit', function(e) {
        const cpf = $('#documento').val();

        if (cpf && !validarCPF(cpf)) {
            e.preventDefault();
            $('#cpf-error').show();
            $('#documento').addClass('is-invalid').focus();
            alert('Por favor, corrija o CPF antes de continuar.');
            return false;
        }
    });
});
