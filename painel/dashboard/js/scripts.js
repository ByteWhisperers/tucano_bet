document.addEventListener('DOMContentLoaded', function () {

//Função Menu	
function openMenu() {
        document.getElementById("mySidebar").style.left = "0";
    }

    function closeMenu() {
        document.getElementById("mySidebar").style.left = "-300px";
    }

    // Torna as funções acessíveis globalmente
    window.openMenu = openMenu;
    window.closeMenu = closeMenu;

    // Seleciona elementos do DOM
const overlay = document.getElementById('overlay');
const modals = document.querySelectorAll('.modal');

const modalGeraPixButton = document.querySelectorAll('.modalGeraPix');
const modalPlayFiverButton = document.querySelectorAll('.modalPlayFiver');
const modalValoresButton = document.querySelectorAll('.modalValores');
const modalFacebookButton = document.querySelectorAll('.modalFacebook');
const modalEmailButton = document.querySelectorAll('.modalEmail');
const modalLogoButton = document.querySelectorAll('.modalLogo');
const modalFaviconButton = document.querySelectorAll('.modalFavicon');
const modalSliderButton = document.querySelectorAll('.modalSlider');
const modalNomeUrlButton = document.querySelectorAll('.modalNomeUrl');
const modalCoresButton = document.querySelectorAll('.modalCores');
const modalRedesButton = document.querySelectorAll('.modalRedes');
const modalNovoJogoButton = document.querySelectorAll('.modalNovoJogo');
const modalSenhaButton = document.querySelectorAll('.btn-senha');

const modalConfirmacaoButton = document.querySelectorAll('.modalConfirmacao');
const modalCancelamentoButton = document.querySelectorAll('.modalCancelamento');
const modalAdicionarButton = document.querySelectorAll('.modalSaldoMaisUsuario');
const modalSubtrairButton = document.querySelectorAll('.modalSaldoMenosUsuario');
const modalEditarButton = document.querySelectorAll('.modalPorcentagemAfiliados');

const modalBonusCadastroButton = document.querySelectorAll('.modalBonusCadastro');
const modalAfiliadosButton = document.querySelectorAll('.modalAfiliados');
const modalDespesasButton = document.querySelectorAll('.modalDespesas');
const modalBonusRaspadinha = document.querySelectorAll('.modalBonusRaspadinha');
const modalStopGames = document.querySelectorAll('.modalStopGames');
const modalEsporte = document.querySelectorAll('.modalEsporte');

const modalEmailMarketing = document.querySelectorAll('.modalEmailMarketing');
const modalExcluirCampanha = document.querySelectorAll('.btn-excluir-campanha');

const modalCampoCel = document.querySelectorAll('.modalCampoCel');

const closeModalButtons = document.querySelectorAll('.close-modal');

// Função para abrir modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        closeModal(); // fecha qualquer modal aberto antes
        modal.classList.add('show');
        overlay.classList.add('show');
    }
}

// Função para limpar formulário dentro do modal
function clearForm(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Função para fechar todos os modais e limpar formulários e alertas
function closeModal() {
    modals.forEach(modal => modal.classList.remove('show'));
    overlay.classList.remove('show');

    modals.forEach(modal => {
        clearForm(modal.id);
    });

    const alertas = [
        '#alerta-gerapix', '#alerta-playfiver', '#alerta-valores',
        '#alerta-facebook', '#alerta-email', '#alerta-logo',
        '#alerta-favicon', '#alerta-slider', '#alerta-nomeurl',
        '#alerta-cores', '#alerta-redes', '#alerta-novojogo',
        '#alerta-senha',
        '#alerta-confirmar',
        '#alerta-saldo-mais',
        '#alerta-saldo-menos',
        '#alerta-porcentagem-afiliado', '#alerta-bonuscadastro',
        '#alerta-afiliados', '#alerta-cancelar', '#alerta-despesas',
        '#alerta-bonusraspadinha', '#alerta-stopgames', '#alerta-esportes',
        '#alerta-emailmarketing', '#alerta-excluircampanha', '#alerta-campocel'
    ];
    alertas.forEach(seletor => {
        const alerta = document.querySelector(seletor);
        if (alerta) {
            alerta.style.display = 'none';
        }
    });
}

// Eventos para abrir cada modal
modalGeraPixButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalGeraPix'));
});
modalPlayFiverButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalPlayFiver'));
});
modalValoresButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalValores'));
});
modalFacebookButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalFacebook'));
});
modalEmailButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalEmail'));
});
modalLogoButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalLogo'));
});
modalFaviconButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalFavicon'));
});
modalNomeUrlButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalNomeUrl'));
});
modalSliderButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalSlider'));
});
modalCoresButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalCores'));
});
modalRedesButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalRedes'));
});
modalNovoJogoButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalNovoJogo'));
});
modalSenhaButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalSenha'));
});

modalAdicionarButton.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const nome = button.getAttribute('data-nome') || '';
        const cpf = button.getAttribute('data-cpf') || '';
        const id = button.getAttribute('data-id') || '';

        const modal = document.getElementById('modalSaldoMaisUsuario');
        if (!modal) return;

        modal.querySelector('#input-nome-mais').value = nome;
        modal.querySelector('#input-cpf-mais').value = cpf;
        modal.querySelector('#saldo-mais-id').value = id;
        modal.querySelector('#input-valor-mais').value = '';

        openModal('modalSaldoMaisUsuario');
    });
});

modalBonusCadastroButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalBonusCadastro'));
});

modalAfiliadosButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalAfiliados'));
});

modalSubtrairButton.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const nome = button.getAttribute('data-nome') || '';
        const cpf = button.getAttribute('data-cpf') || '';
        const id = button.getAttribute('data-id') || '';

        const modal = document.getElementById('modalSaldoMenosUsuario');
        if (!modal) return;

        modal.querySelector('#input-nome-menos').value = nome;
        modal.querySelector('#input-cpf-menos').value = cpf;
        modal.querySelector('#saldo-menos-id').value = id;
        modal.querySelector('#input-valor-menos').value = '';

        openModal('modalSaldoMenosUsuario');
    });
});

modalEditarButton.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const nome = button.getAttribute('data-nome') || '';
        const cpf = button.getAttribute('data-cpf') || '';
        const id = button.getAttribute('data-id') || '';

        const modal = document.getElementById('modalPorcentagemAfiliados');
        if (!modal) return;

        modal.querySelector('#input-nome').value = nome;
        modal.querySelector('#input-cpf').value = cpf;
        modal.querySelector('#porcentagem-afiliado-id').value = id;
        modal.querySelector('#select-porcentagem').value = '';

        openModal('modalPorcentagemAfiliados');
    });
});

modalConfirmacaoButton.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();

        const nome = button.getAttribute('data-nome') || '';
        const cpf = button.getAttribute('data-cpf') || '';
        const valor = button.getAttribute('data-valor') || '';
        const id = button.getAttribute('data-id') || '';

        document.getElementById('input-nome').value = nome;
        document.getElementById('input-cpf').value = cpf;
        document.getElementById('input-valor').value = 'R$ ' + valor;
        document.getElementById('extrato-id').value = id;

        // 🛠 Atualiza botão de cancelamento
        const botaoCancelar = document.querySelector('.modalCancelamento');
        if (botaoCancelar) {
            botaoCancelar.setAttribute('data-nome', nome);
            botaoCancelar.setAttribute('data-cpf', cpf);
            botaoCancelar.setAttribute('data-valor', valor);
            botaoCancelar.setAttribute('data-id', id);

            // Remover antigo evento (evita múltiplos binds)
            botaoCancelar.replaceWith(botaoCancelar.cloneNode(true));
            const novoBotaoCancelar = document.querySelector('.modalCancelamento');

            novoBotaoCancelar.addEventListener('click', function (e) {
                e.preventDefault();

                document.getElementById('cancelar-nome').value = nome;
                document.getElementById('cancelar-cpf').value = cpf;
                document.getElementById('cancelar-valor').value = 'R$ ' + valor;
                document.getElementById('cancelar-id').value = id;

                openModal('modalCancelamento');
            });
        }

        openModal('modalConfirmacao');
    });
});

modalDespesasButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalDespesas'));
});

modalBonusRaspadinha.forEach(button => {
    button.addEventListener('click', () => openModal('modalBonusRaspadinha'));
});

modalStopGames.forEach(button => {
    button.addEventListener('click', () => openModal('modalStopGames'));
});

modalEsporte.forEach(button => {
    button.addEventListener('click', () => openModal('modalEsporte'));
});

modalEmailMarketing.forEach(button => {
    button.addEventListener('click', () => openModal('modalEmailMarketing'));
});

const modalExcluirCampanhaButtons = document.querySelectorAll('.btn-excluir-campanha');

modalExcluirCampanhaButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();

        const id   = button.getAttribute('data-id') || '';
        const nome = button.getAttribute('data-nome') || '';

        const modal = document.getElementById('modalExcluirCampanha');
        if (!modal) return;

        modal.querySelector('#excluir-campanha-id').value = id;
        modal.querySelector('#excluir-campanha-nome').textContent = nome;

        openModal('modalExcluirCampanha');
    });
});

modalCampoCel.forEach(button => {
    button.addEventListener('click', () => openModal('modalCampoCel'));
});



// Evento global para abrir pelos cliques em classes
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modalGeraPix')) {
        openModal('modalGeraPix');
    } else if (e.target.classList.contains('modalPlayFiver')) {
        openModal('modalPlayFiver');
    } else if (e.target.classList.contains('modalValores')) {
        openModal('modalValores');
    } else if (e.target.classList.contains('modalFacebook')) {
        openModal('modalFacebook');
    } else if (e.target.classList.contains('modalEmail')) {
        openModal('modalEmail');
    } else if (e.target.classList.contains('modalLogo')) {
        openModal('modalLogo');
    } else if (e.target.classList.contains('modalFavicon')) {
        openModal('modalFavicon');
    } else if (e.target.classList.contains('modalSlider')) {
        openModal('modalSlider');
    } else if (e.target.classList.contains('modalNomeUrl')) {
        openModal('modalNomeUrl');
    } else if (e.target.classList.contains('modalCores')) {
        openModal('modalCores');
    } else if (e.target.classList.contains('modalRedes')) {
        openModal('modalRedes');
    } else if (e.target.classList.contains('modalNovoJogo')) {
        openModal('modalNovoJogo');
    } else if (e.target.classList.contains('modalSenha')) {
        openModal('modalSenha');
    } else if (e.target.classList.contains('modalConfirmacao')) {
        openModal('modalConfirmacao');
    } else if (e.target.classList.contains('modalCancelamento')) {
        openModal('modalCancelamento');
    } else if (e.target.classList.contains('modalSaldoMaisUsuario')) {
        openModal('modalSaldoMaisUsuario');
    } else if (e.target.classList.contains('modalSaldoMenosUsuario')) {
        openModal('modalSaldoMenosUsuario');
    } else if (e.target.classList.contains('modalPorcentagemAfiliados')) {
        openModal('modalPorcentagemAfiliados');
    } else if (e.target.classList.contains('modalBonusCadastro')) {
        openModal('modalBonusCadastro');
    } else if (e.target.classList.contains('modalAfiliados')) {
        openModal('modalAfiliados');
    }else if (e.target.classList.contains('modalDespesas')) {
        openModal('modalDespesas');
    }else if (e.target.classList.contains('modalBonusRaspadinha')) {
        openModal('modalBonusRaspadinha');
    }else if (e.target.classList.contains('modalStopGames')) {
        openModal('modalStopGames');
    }else if (e.target.classList.contains('modalEsporte')) {
        openModal('modalEsporte');
    }else if (e.target.classList.contains('modalEmailMarketing')) {
        openModal('modalEmailMarketing');
    }else if (e.target.classList.contains('modalExcluirCampanha')) {
        openModal('modalExcluirCampanha');
    }else if (e.target.classList.contains('modalCampoCel')) {
        openModal('modalCampoCel');
    }
});

// Botões de fechar modal
closeModalButtons.forEach(button => {
    button.addEventListener('click', closeModal);
});

overlay.addEventListener('click', closeModal);

// Função forms 
     $(document).ready(function () {
    $('#formgerapix, #formplayfiver, #formvalores, #formfacebook, #formemail, #formlogo, #formfavicon, #formslider, #formnomeurl, #formcores, #formredes, #formnovojogo, #formsenha, #formconfirmar, #formsaldomais, #formsaldomenos, #formporcentagemafiliado, #formbonuscadastro, #formafiliados, #formcancelar, #formdespesas, #formbonusraspadinha, #formstopgames, #formesportes, #formemailmarketing, #formexcluircampanha, #formcampocel').submit(function (e) {
        e.preventDefault();

        var alertaId = '';
        var submitButton = '';
        var temProgresso = false;

        switch ($(this).attr('id')) {
            case 'formgerapix':
                alertaId = '#alerta-gerapix';
                submitButton = '#subGerapix';
                break;
            case 'formplayfiver':
                alertaId = '#alerta-playfiver';
                submitButton = '#subPlayfiver';
                break;
            case 'formvalores':
                alertaId = '#alerta-valores';
                submitButton = '#subValores';
                break;    
            case 'formfacebook':
                alertaId = '#alerta-facebook';
                submitButton = '#subFacebook';
                break;    
            case 'formemail':
                alertaId = '#alerta-email';
                submitButton = '#subEmail';
                break;    
            case 'formlogo':
                alertaId = '#alerta-logo';
                submitButton = '#subLogo';
                temProgresso = true;
                break;
            case 'formfavicon':
                alertaId = '#alerta-favicon';
                submitButton = '#subFavicon';
                temProgresso = true;
                break;
            case 'formslider':
                alertaId = '#alerta-slider';
                submitButton = '#subSlider';
                temProgresso = true;
                break;    
            case 'formnomeurl':
                alertaId = '#alerta-nomeurl';
                submitButton = '#subNomeUrl';
                break;
            case 'formcores':
                alertaId = '#alerta-cores';
                submitButton = '#subCores';
                break;    
            case 'formredes':
                alertaId = '#alerta-redes';
                submitButton = '#subRedes';
                break;  
            case 'formnovojogo':
                alertaId = '#alerta-novojogo';
                submitButton = '#subNovoJogo';
                break;  
            case 'formsenha':
                alertaId = '#alerta-senha';
                submitButton = '#subSenha';
                break; 
            case 'formconfirmar':
                alertaId = '#alerta-confirmar';
                submitButton = '#subConfirmacao';
                break;  
            case 'formcancelar':
                alertaId = '#alerta-cancelar';
                submitButton = '#subCancelamento';
                break;  
            case 'formsaldomais':
                alertaId = '#alerta-saldo-mais';
                submitButton = '#subSaldoMais';
                break;
            case 'formsaldomenos':
                alertaId = '#alerta-saldo-menos';
                submitButton = '#subSaldoMenos';
                break;
            case 'formporcentagemafiliado':
                alertaId = '#alerta-porcentagem-afiliado';
                submitButton = '#subPorcentagemAfiliados';
                break;
            case 'formbonuscadastro':
                alertaId = '#alerta-bonuscadastro';
                submitButton = '#subBonusCadastro';
                break;
            case 'formafiliados':
                alertaId = '#alerta-afiliados';
                submitButton = '#subAfiliados';
                break;
            case 'formdespesas':
                alertaId = '#alerta-despesas';
                submitButton = '#subDespesas';
                break; 
            case 'formbonusraspadinha':
                alertaId = '#alerta-bonusraspadinha';
                submitButton = '#subBonusRaspadinha';
                break;   
            case 'formstopgames':
                alertaId = '#alerta-stopgames';
                submitButton = '#subStopGames';
                break; 
            case 'formesportes':
                alertaId = '#alerta-esportes';
                submitButton = '#subEsporte';
                break;  
            case 'formemailmarketing':
                alertaId = '#alerta-emailmarketing';
                submitButton = '#subEmailMarketing';
                break; 
            case 'formexcluircampanha':
                alertaId = '#alerta-excluircampanha';
                submitButton = '#subExcluirCampanha';
                break;  
            case 'formcampocel':
                alertaId = '#alerta-campocel';
                submitButton = '#subCampoCel';
                break;                                                                                                                             
        }

        $(submitButton).prop('disabled', true);

        var formData = new FormData(this);

        if (temProgresso) {
            // Mostra a barra e zera a largura dela
            $(this).find('.progress-container').show();
            $(this).find('.progress-bar').css('width', '0%');
        }

        $.ajax({
            url: $(this).attr('action'),
            data: formData,
            processData: false,
            type: 'POST',
            contentType: false,
            dataType: 'json',

            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                if (temProgresso) {
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            // Atualiza a barra de progresso
                            $(e.target).find('.progress-bar').css('width', percentComplete + '%');
                        }
                    }, false);
                }

                return xhr;
            },

            success: function (retorno) {
                if (retorno.status === 'alertasim') {
                    $(alertaId).html(retorno.message).css('display', 'block');
                    $('#' + e.target.id).trigger('reset');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else if (retorno.status === 'alertanao') {
                    $(alertaId).html(retorno.message).css('display', 'block');
                }
            },
            error: function () {
                $(alertaId).html("<p class='alertanao'>Ocorreu um erro. Tente novamente! <span><i class='fas fa-times'></i></span></p>")
                    .css('display', 'block');
            },
            complete: function () {
                if (temProgresso) {
                    // Esconde a barra de progresso
                    $(e.target).find('.progress-container').hide();
                }
                $(submitButton).prop('disabled', false);
            }
        });
    });
});

//Função dos alertas
 $(function() {
  $('#alerta-gerapix,#alerta-playfiver,#alerta-valores,#alerta-facebook,#alerta-email,#alerta-logo,#alerta-favicon,#alerta-slider, #alerta-nomeurl, #alerta-cores, #alerta-redes, #alerta-novojogo, #alerta-senha, #alerta-confirmar, #alerta-cancelar, #alerta-saldo-mais,#alerta-saldo-menos,#alerta-porcentagem-afiliado, #alerta-bonuscadastro, #alerta-afiliados, #alerta-despesas, #alerta-bonusraspadinha, #alerta-stopgames, #alerta-esportes, #alerta-emailmarketing, #alerta-excluircampanha, #alerta-campocel').click(function() {
    closeAlert();
     });
    });
 
     function closeAlert() {
     $('#alerta-gerapix,#alerta-playfiver,#alerta-valores,#alerta-facebook,#alerta-email,#alerta-logo,#alerta-favicon,#alerta-slider, #alerta-nomeurl, #alerta-cores, #alerta-redes, #alerta-novojogo, #alerta-senha, #alerta-confirmar, #alerta-cancelar, #alerta-saldo-mais,#alerta-saldo-menos,#alerta-porcentagem-afiliado, #alerta-bonuscadastro, #alerta-afiliados, #alerta-despesas, #alerta-bonusraspadinha, #alerta-stopgames, #alerta-esportes, #alerta-emailmarketing, #alerta-excluircampanha, #alerta-campocel').fadeOut();
    } 

// Função para lidar com a formatação de valores
function formatCurrency(inputField) {
    inputField.addEventListener('input', function (e) {
        // Remove todos os caracteres não numéricos
        let value = e.target.value.replace(/\D/g, '');

        // Se o valor não for um número válido, limpa o campo
        if (value === '') {
            e.target.value = ''; // Permite que o placeholder apareça
            return;
        }

        // Converte para número e divide por 100 para obter o valor em reais
        const numericValue = parseFloat(value) / 100;

        // Formata o valor como moeda BRL
        const formattedValue = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(numericValue);

        // Define o valor formatado no campo de entrada
        e.target.value = formattedValue;
    });
}

// IDs dos campos que podem existir na página
const camposParaFormatar = [
    'deposito',
    'retirada',
    'input-valor-mais',
    'input-valor-menos',
    'bonuscadastro',
    'valor-despesa'
];

// Aplica a formatação apenas se o campo existir
camposParaFormatar.forEach(id => {
    const campo = document.getElementById(id);
    if (campo) {
        formatCurrency(campo);
    }
});

//Função copia Link
document.querySelectorAll('.copy-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const linkText = document.getElementById(targetId).innerText;

        navigator.clipboard.writeText(linkText).then(() => {
            const originalText = btn.textContent;
            btn.textContent = 'Link Copiado!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        });
    });
});

//Função busca da página usuários 
$(document).ready(function(){
  var $input = $('input[name="emailcpf"]');

  function setPlaceholderAndMask(tipo) {
    if (tipo === 'cpf') {
      $input.attr('placeholder', 'Digite o CPF');
      $input.mask('000.000.000-00');
    } else {
      $input.attr('placeholder', 'Digite o e-mail');
      $input.unmask();
    }
  }

  setPlaceholderAndMask($('select[name="tipo"]').val());

  $('select[name="tipo"]').on('change', function(){
    var tipo = $(this).val();
    $input.val('');
    setPlaceholderAndMask(tipo);
  });
});

});