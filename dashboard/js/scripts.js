document.addEventListener('DOMContentLoaded', function () {

    // Funções abrir modais
const overlay = document.getElementById('overlay');
const modals = document.querySelectorAll('.modal');

const modalDepositoButton = document.querySelectorAll('.modalDeposito');
const modalRetiradaButton = document.querySelectorAll('.modalRetirada');
const modalDadosButton = document.querySelectorAll('.btn-dados');
const modalSenhaButton = document.querySelectorAll('.btn-senha');        
const modalContatoButton = document.querySelectorAll('.modalContato');
const modalAfiliadoButton = document.querySelectorAll('.resgatar');
const modalBonusButton = document.querySelectorAll('.modalBonus');
const modalRaspadinhaButton = document.querySelectorAll('.modalRaspadinha');

const closeModalButtons = document.querySelectorAll('.close-modal');

function bloquearScroll(e) {
    e.preventDefault();
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        closeModal();
        modal.classList.add('show');

        if (modalId === 'modalRaspadinha') {
            overlay.classList.add('show');
            document.body.classList.add('body-no-scroll');
            document.addEventListener('touchmove', bloquearScroll, { passive: false });

            if (typeof iniciarRaspadinha === 'function') {
                iniciarRaspadinha();
            }
        } else {
            overlay.classList.add('show');
        }
    }
}

function clearForm(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

function closeModal() {
    modals.forEach(modal => modal.classList.remove('show'));
    overlay.classList.remove('show');
    document.body.classList.remove('body-no-scroll');
    document.removeEventListener('touchmove', bloquearScroll);

    modals.forEach(modal => {
        clearForm(modal.id);
    });

    const alertas = [
        '#alerta-deposito',
        '#alerta-retirada',
        '#alerta-dados',
        '#alerta-senha',
        '#alerta-contato',
        '#alerta-afiliados',
        '#alerta-bonus',
        '#alerta-raspadinha'
    ];
    alertas.forEach(seletor => {
        const alerta = document.querySelector(seletor);
        if (alerta) {
            alerta.style.display = 'none';
        }
    });
}

modalDepositoButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalDeposito'));
});
modalRetiradaButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalRetirada'));
});
modalDadosButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalDados'));
});
modalSenhaButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalSenha'));
});
modalContatoButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalContato'));
});
modalAfiliadoButton.forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const nome = this.dataset.nome;
        const valor = this.dataset.valor;

        document.getElementById('afiliado-id').value = id;
        document.getElementById('afiliado-nome').value = nome;
        document.getElementById('afiliado-bonus').value = 'R$ ' + valor;

        openModal('modalAfiliados');
    });
});
modalBonusButton.forEach(button => {
    button.addEventListener('click', () => openModal('modalBonus'));
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modalDeposito')) {
        openModal('modalDeposito');
    } else if (e.target.classList.contains('modalRetirada')) {
        openModal('modalRetirada');
    } else if (e.target.classList.contains('modalDados')) {
        openModal('modalDados');
    } else if (e.target.classList.contains('modalSenha')) {
        openModal('modalSenha');
    } else if (e.target.classList.contains('modalContato')) {
        openModal('modalContato');
    } else if (e.target.classList.contains('modalBonus')) {
        openModal('modalBonus');
    } else if (e.target.classList.contains('modalRaspadinha')) {
        openModal('modalRaspadinha');
    } else if (e.target.classList.contains('resgatar')) {
        const id = e.target.dataset.id;
        const nome = e.target.dataset.nome;
        const valor = e.target.dataset.valor;

        document.getElementById('afiliado-id').value = id;
        document.getElementById('afiliado-nome').value = nome;
        document.getElementById('afiliado-bonus').value = 'R$ ' + valor;

        openModal('modalAfiliados');
    }
});

closeModalButtons.forEach(button => {
    button.addEventListener('click', closeModal);
});

overlay.addEventListener('click', closeModal);

if (!usuarioTemBonus) {
    openModal('modalRaspadinha');
}


// Função abre os termos e fecha o menu se estiver aberto
    function abrirSidebar(conteudoId) {
        // Fecha o menu lateral se estiver aberto
        document.getElementById("mySidebar").style.left = "-300px";

        $('#sidebartermo').addClass('show');

        $.ajax({
            url: '../termo/' + conteudoId + '.php',
            success: function (data) {
                $('#sidebartermoContent').html(data);
            },
            error: function () {
                $('#sidebartermoContent').html('<p>Erro ao carregar o conteúdo.</p>');
            }
        });
    }

// Função abre o menu e fecha os termos se estiver aberto
    function openMenu() {
        // Fecha o sidebar termo se estiver aberto
        $('#sidebartermo').removeClass('show');

        document.getElementById("mySidebar").style.left = "0";
    }

    function closeMenu() {
        document.getElementById("mySidebar").style.left = "-300px";
    }

    // Torna as funções acessíveis globalmente
    window.openMenu = openMenu;
    window.closeMenu = closeMenu;

    $(document).ready(function () {
        $(document).on('click', '#termo-condicao, #termo-privacidade, #termo-cookies, #termo-18anos, #termo-jogo-responsavel', function (e) {
            e.preventDefault();
            const conteudoId = $(this).attr('id');
            abrirSidebar(conteudoId);
        });

        // Fechar sidebar termo
        $(document).on('click', '.close-sidebartermo', function () {
            $('#sidebartermo').removeClass('show');
        });
    });

// Função para lidar com a formatação de valores
function formatCurrency(inputField) {
    inputField.addEventListener('input', function (e) {
    
        let value = e.target.value.replace(/\D/g, '');

        if (value === '') {
            e.target.value = '';
            return;
        }

        const numericValue = parseFloat(value) / 100;

        const formattedValue = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(numericValue);

        e.target.value = formattedValue;
    });
}

formatCurrency(document.getElementById('deposito'));
formatCurrency(document.getElementById('retirada'));


// Função executa os forms
$(document).ready(function () {
  
    $('#formdeposito, #formretirada, #formsenha, #formdados, #formcontato, #formafiliados, #formbonus, #formraspadinha').submit(function (e) {
        e.preventDefault();

        var alertaId = '';
        var submitButton = '';

        switch ($(this).attr('id')) {
            case 'formdeposito':
                alertaId = '#alerta-deposito';
                submitButton = '#subDeposito';
                break;
            case 'formretirada':
                alertaId = '#alerta-retirada';
                submitButton = '#subRetirada';
                break;
            case 'formdados':
                alertaId = '#alerta-dados';
                submitButton = '#subDados';
                break;    
            case 'formsenha':
                alertaId = '#alerta-senha';
                submitButton = '#subSenha';
                break;
            case 'formcontato':
                alertaId = '#alerta-contato';
                submitButton = '#subContato';
                break;
            case 'formafiliados':
                alertaId = '#alerta-afiliados';
                submitButton = '#subAfiliados';
                break; 
            case 'formbonus':
                alertaId = '#alerta-bonus';
                submitButton = '#subBonus';
                break;
            case 'formraspadinha':
                alertaId = '#alerta-raspadinha';
                submitButton = '#subRaspadinha';
                break;      
        }

        $(submitButton).prop('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            data: formData,
            processData: false,
            type: 'POST',
            contentType: false,
            dataType: 'json', 
            success: function (retorno) {
                if (retorno.status === 'alertasim') {
                    $(alertaId).html(retorno.message).css('display', 'block');

            if (e.target.id === 'formdeposito') {
                $('#modalDeposito .modal-content').html(retorno.html);
                $('#modalDeposito .close-modal').on('click', function () {
                    closeModal();         
                    location.reload(); 
                });
            } else{
                setTimeout(function () {
                        location.reload();
                }, 1500);
            }

                    $('#' + e.target.id).trigger('reset');
                } else if (retorno.status === 'alertanao') {
                    $(alertaId).html(retorno.message).css('display', 'block');
                }
            },
            error: function () {
                $(alertaId).html("<p class='alertanao'>Ocorreu um erro. Tente novamente! <span><i class='fas fa-times'></i></span></p>")
                    .css('display', 'block');
            },
            complete: function () {
                $(submitButton).prop('disabled', false);
            }
        });
    });
});

// Função para fechar os alertas dos forms
 $(function() {
  $('#alerta-deposito, #alerta-retirada, #alerta-dados, #alerta-senha, #alerta-contato, #alerta-afiliados, #alerta-bonus, #alerta-raspadinha').click(function() {
    closeAlert();
  });
});
 
function closeAlert() {
  $('#alerta-deposito, #alerta-retirada, #alerta-dados, #alerta-senha,  #alerta-contato, #alerta-afiliados, #alerta-bonus, #alerta-raspadinha').fadeOut();
}


//Funções do Slider
    const slides = document.getElementById('slides');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = dots.length;
    let currentIndex = 0;
    let slideInterval;

    function showSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        slides.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
        currentIndex = index;
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function stopSlideShow() {
        clearInterval(slideInterval);
    }

    dots.forEach(dot => {
        dot.addEventListener('click', (e) => {
            stopSlideShow();
            const index = parseInt(e.target.getAttribute('data-index'));
            showSlide(index);
            startSlideShow();
        });
    });

    showSlide(0);
    startSlideShow();

// Funções slider dos maiores ganhos de hoje
  const slider = document.getElementById('ganhos-slider');

let scrollAmount = 0;
const speed = 2;
const fps = 60;

function loop() {
  scrollAmount += speed;
  if (scrollAmount >= slider.scrollWidth / 2) {
    scrollAmount = 0;
  }
  slider.style.transform = `translateX(-${scrollAmount}px)`;
  requestAnimationFrame(loop);
}

requestAnimationFrame(loop);    

// Funções campo busca
    $(document).ready(function () {
    $('#input-busca').on('input', function () {
        var busca = $(this).val();
        if (busca.length > 1) {
            $.ajax({
                url: 'funcoes/buscar.php',
                method: 'POST',   // mudou para POST
                data: { q: busca },
                success: function (data) {
                    $('#resultado-busca').html(data).show();
                }
            });
        } else {
            $('#resultado-busca').hide();
        }
    });
});

 // Funções gera número de jogadores onlines (busca,cards)
  function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
  }

  function updateOnlineNumbers() {
    const onlineNumbers = document.querySelectorAll('.online-number');
    onlineNumbers.forEach(el => {
      let currentValue = parseInt(el.textContent) || getRandomInt(1, 80);
      let variation = Math.floor(Math.random() * 7) - 3; // -3 a +3
      let newValue = clamp(currentValue + variation, 1, 80);
      el.textContent = newValue;
    });
  }

  function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  setInterval(updateOnlineNumbers, 1000);

  document.addEventListener('DOMContentLoaded', () => {
    const onlineNumbers = document.querySelectorAll('.online-number');
    onlineNumbers.forEach(el => {
      el.textContent = getRandomInt(1, 80);
    });
    updateOnlineNumbers();
  });

//Função que abre os jogos 
  document.addEventListener('click', function(event) {
  if (event.target.closest('.jogar-btn, .btn-jogar')) {
    const button = event.target.closest('.jogar-btn, .btn-jogar');
    const gameId = button.getAttribute('data-game-id');
    console.log('Botão jogar clicado, gameId:', gameId);
    updateUserBalanceAndLaunchGame(gameId);
  }
});

function updateUserBalanceAndLaunchGame(gameId) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/launch_game.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('game_id=' + encodeURIComponent(gameId));

  xhr.onload = function() {
    if (xhr.status === 200) {
      try {
        console.log('Resposta do launch_game.php:', xhr.responseText);
        const response = JSON.parse(xhr.responseText);
        if (response.status === 'success') {
          console.log('Chamando openGameModal com URL:', response.launch_url);
          openGameModal(response.launch_url);
        } else {
          alert("Erro ao iniciar o jogo: " + response.msg);
        }
      } catch(e) {
        alert("Erro no retorno do servidor.");
      }
    } else {
      alert("Erro na requisição. Status: " + xhr.status);
    }
  };

  xhr.onerror = function() {
    alert("Erro de comunicação com o servidor.");
  };
}

function openGameModal(url) { 
  const modal = document.getElementById('modal');
  modal.classList.add('show');
  console.log('URL recebida para o iframe:', url); 
  const iframe = document.getElementById('iframe');
  iframe.src = url;
}

function closeGameModal() {
  const modal = document.getElementById('modal');
  modal.classList.remove('show');

  const iframe = document.getElementById('iframe');
  iframe.src = "";

  location.reload();
}
window.closeGameModal = closeGameModal;

// Funções botão Ver mais do footer
     document.getElementById("toggleButton").addEventListener("click", function() {
    const extra = document.getElementById("extraContent");
    const button = this;
    
    if (extra.style.display === "none") {
        extra.style.display = "block";
        button.textContent = "Ver menos";
    } else {
        extra.style.display = "none";
        button.textContent = "Ver mais";
    }
});

     // Função copiar código PIX
function copiarCodigo() {
    const input = document.getElementById('pixLink');
    if (!input) return;

    input.select();
    input.setSelectionRange(0, 99999); // Compatível com mobile

    document.execCommand('copy');

    const botao = document.querySelector('.BotaoCopiaPix');
    if (!botao) return;

    botao.textContent = 'Código Copiado';

    setTimeout(() => {
        botao.textContent = 'Copiar Código';
    }, 2000);
}

window.copiarCodigo = copiarCodigo;

// Função Raspadinha
const valoresPossiveis = [10, 20, 30, 40, 50];
const numerosDiv = document.getElementById('raspadinha-numeros');
const resultado = document.getElementById('raspadinha-resultado');
const valoresSorteados = [];
const divs = [];

let valorPremiado = valoresPossiveis[Math.floor(Math.random() * valoresPossiveis.length)];
let indicesPremiados = [];

while (indicesPremiados.length < 3) {
  const idx = Math.floor(Math.random() * 9);
  if (!indicesPremiados.includes(idx)) {
    indicesPremiados.push(idx);
  }
}

let usadosNaoPremiados = new Set();

for (let i = 0; i < 9; i++) {
  let valor;
  if (indicesPremiados.includes(i)) {
    valor = valorPremiado;
  } else {
    let outro;
    let tentativas = 0;
    do {
      outro = valoresPossiveis[Math.floor(Math.random() * valoresPossiveis.length)];
      tentativas++;
    } while ((outro === valorPremiado || usadosNaoPremiados.has(outro)) && tentativas < 100);

    usadosNaoPremiados.add(outro);
    valor = outro;
  }

  valoresSorteados.push(valor);
  const div = document.createElement('div');
  div.className = 'raspadinha-numero';
  div.textContent = `R$ ${valor}`;
  div.dataset.index = i;
  divs.push(div);
  numerosDiv.appendChild(div);
}

const canvas = document.getElementById('raspadinha-canvas');
const ctx = canvas.getContext('2d');

// Ajusta o tamanho do canvas de acordo com o container
const container = document.getElementById('raspadinha-container');
canvas.width = container.offsetWidth;
canvas.height = container.offsetHeight;

ctx.fillStyle = '#121212';
ctx.fillRect(0, 0, canvas.width, canvas.height);

desenharTexturaSeguranca(ctx, canvas.width, canvas.height);

ctx.font = 'bold 36px sans-serif';
ctx.fillStyle = '#dedede';
ctx.textAlign = 'center';
ctx.textBaseline = 'middle';
ctx.fillText('RASPE AQUI', canvas.width / 2, canvas.height / 2);

ctx.globalCompositeOperation = 'destination-out';

let isDrawing = false;
let revelado = false;

const trackingCanvas = document.createElement('canvas');
trackingCanvas.width = canvas.width;
trackingCanvas.height = canvas.height;
const trackingCtx = trackingCanvas.getContext('2d');

const cellSize = canvas.width / 3;
const raspados = new Set();

function getPos(e) {
  const rect = canvas.getBoundingClientRect();
  const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
  const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
  return { x, y };
}

function draw(e) {
  if (!isDrawing) return;
  const pos = getPos(e);
  ctx.beginPath();
  ctx.arc(pos.x, pos.y, 20, 0, 2 * Math.PI);
  ctx.fill();

  const col = Math.floor(pos.x / cellSize);
  const row = Math.floor(pos.y / cellSize);
  const index = row * 3 + col;
  raspados.add(index);

  trackingCtx.fillStyle = '#000';
  trackingCtx.beginPath();
  trackingCtx.arc(pos.x, pos.y, 20, 0, 2 * Math.PI);
  trackingCtx.fill();

  verificarRaspagem();
}

function iniciar(e) {
  isDrawing = true;
  draw(e);
}

function parar() {
  isDrawing = false;
}

canvas.addEventListener('mousedown', iniciar);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', parar);
canvas.addEventListener('touchstart', iniciar);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', parar);

function desenharTexturaSeguranca(ctx, largura, altura) {
  ctx.strokeStyle = '#1C1C1C';
  ctx.lineWidth = 1;
  for (let y = 0; y < altura; y += 10) {
    ctx.beginPath();
    for (let x = 0; x <= largura; x += 10) {
      const wave = Math.sin((x + y) / 10) * 4;
      ctx.lineTo(x, y + wave);
    }
    ctx.stroke();
  }
  ctx.strokeStyle = '#202020';
  for (let x = 0; x < largura; x += 10) {
    ctx.beginPath();
    for (let y = 0; y <= altura; y += 10) {
      const wave = Math.cos((x + y) / 10) * 4;
      ctx.lineTo(x + wave, y);
    }
    ctx.stroke();
  }
}

function verificarRaspagem() {
  if (revelado) return;

  let contador = 0;
  for (const i of raspados) {
    if (valoresSorteados[i] === valorPremiado) {
      contador++;
    }
  }

  if (contador >= 3) {
    revelado = true;
    resultado.textContent = `🎉 Parabéns! Você ganhou R$${valorPremiado} de bônus!`;
    document.getElementById('subRaspadinha').style.display = 'inline-block';

    const inputBonus = document.getElementById('valor-bonus');
    if (inputBonus) {
      inputBonus.value = valorPremiado;
    }
  }
}



});	


// Sistema de Afiliação Gamificado - Baús
document.addEventListener('DOMContentLoaded', function() {
    // Handler para botões de abrir baú
    const botoesBaus = document.querySelectorAll('.btn-abrir-bau');
    
    botoesBaus.forEach(botao => {
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            const bauId = this.getAttribute('data-bau-id');
            resgatarBau(bauId);
        });
    });
});

function resgatarBau(bauId) {
    const csrfToken = document.querySelector('input[name="csrf_token_resgatar_bau"]');
    if (!csrfToken) {
        console.error('CSRF token não encontrado');
        return;
    }

    const formData = new FormData();
    formData.append('bau_id', bauId);
    formData.append('csrf_token', csrfToken.value);

    fetch('php/resgatar_bau.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'alertasim') {
            // Mostrar alerta de sucesso
            showAlert(data.message, 'success');
            
            // Atualizar saldo no header
            const saldoElement = document.querySelector('[data-saldo]');
            if (saldoElement) {
                saldoElement.textContent = 'R$ ' + data.novo_saldo;
            }
            
            // Recarregar a página após 2 segundos
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Ocorreu um erro ao resgatar o baú. Tente novamente.', 'error');
    });
}

function showAlert(message, type) {
    // Criar elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 4px;
        z-index: 9999;
        animation: slideIn 0.3s ease-in-out;
    `;
    
    document.body.appendChild(alertDiv);
    
    // Remover após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
