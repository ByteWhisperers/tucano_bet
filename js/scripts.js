document.addEventListener('DOMContentLoaded', function () {

// Funções abrir modais
    const overlay = document.getElementById('overlay');
    const modals = document.querySelectorAll('.modal');
    const modalLoginButton = document.querySelectorAll('.modalLogin');
    const modalCadastroButton = document.querySelectorAll('.modalCadastro');
    const modalContatoButton = document.querySelectorAll('.modalContato');
    const modalRecuperarSenhaButton = document.querySelectorAll('.modalRecuperarSenha');
    const closeModalButtons = document.querySelectorAll('.close-modal');

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            closeModal();
            modal.classList.add('show');
            overlay.classList.add('show');
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

        modals.forEach(modal => {
            clearForm(modal.id);
        });

        const alertas = ['#alerta-contato', '#alerta-cadastro', '#alerta-login', '#alerta-senha'];
        alertas.forEach(seletor => {
            const alerta = document.querySelector(seletor);
            if (alerta) {
                alerta.style.display = 'none';
            }
        });
    }

    modalLoginButton.forEach(button => {
        button.addEventListener('click', () => openModal('modalLogin'));
    });

    modalCadastroButton.forEach(button => {
        button.addEventListener('click', () => openModal('modalCadastro'));
    });

    modalContatoButton.forEach(button => {
        button.addEventListener('click', () => openModal('modalContato'));
    });

    modalRecuperarSenhaButton.forEach(button => {
        button.addEventListener('click', () => openModal('modalRecuperarSenha'));
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modalLogin')) {
            openModal('modalLogin');
        } else if (e.target.classList.contains('modalCadastro')) {
            openModal('modalCadastro');
        } else if (e.target.classList.contains('modalContato')) {
            openModal('modalContato');
        } else if (e.target.classList.contains('modalRecuperarSenha')) {
            openModal('modalRecuperarSenha');
        }
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });

    overlay.addEventListener('click', closeModal);


// Funções para envio dos modais
     $(document).ready(function () {
    $('#formcadastro, #formlogin, #formsenha, #formcontato').submit(function (e) {
        e.preventDefault();

        var alertaId = '';
        var submitButton = '';

        switch ($(this).attr('id')) {
            case 'formcadastro':
                alertaId = '#alerta-cadastro';
                submitButton = '#subCadastro';
                break;
            case 'formlogin':
                alertaId = '#alerta-login';
                submitButton = '#subLogin';
                break;
            case 'formsenha':
                alertaId = '#alerta-senha';
                submitButton = '#subSenha';
                break;
            case 'formcontato':
                alertaId = '#alerta-contato';
                submitButton = '#subContato';
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

                    $('#' + e.target.id).trigger('reset');

                    setTimeout(function () {
                        window.location.href = window.location.origin;
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
                $(submitButton).prop('disabled', false);
            }
        });
    });
});


// Funções para o modal de cadastro iniciar aberto
      function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
    }

     const modalParam = getQueryParam('modal');
      if (modalParam === 'cadastro' && typeof openModal === 'function') {
    openModal('modalCadastro');
    }

// Funções máscaras CPF e nascimento
    $('#cpf').mask('000.000.000-00');
    $('#cpflogin').mask('000.000.000-00');
    $('#cpfrecuperar').mask('000.000.000-00');
    $('#nascimento').mask('00/00/0000');    

    // Máscara celular (só se o campo existir)
    if ($('#celular').length) {
    $('#celular').mask('(00) 00000-0000');
    }


     $(function() {
  $('#alerta-contato,#alerta-cadastro,#alerta-login,#alerta-senha').click(function() {
    closeAlert();
     });
    });
 
     function closeAlert() {
     $('#alerta-contato,#alerta-cadastro,#alerta-login,#alerta-senha').fadeOut();
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
      // Calcula uma pequena variação entre -3 e +3
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

// Funções do ver todos os jogos , botão ver mais 
let offset = 0;
const limit = 6;

document.getElementById('verMaisBtn').addEventListener('click', carregarMais);

function carregarMais() {
  fetch(`funcoes/carregar-jogos.php?offset=${offset}&limit=${limit}`)
    .then(res => res.json())
    .then(jogos => {
      const container = document.getElementById('jogosCarregaveis');

      if (jogos.length === 0) {
        document.getElementById('verMaisBtn').style.display = 'none';
        return;
      }

      jogos.forEach(jogo => {
        const card = document.createElement('div');
        card.className = 'jogo-card';
        card.innerHTML = `
          <img src="../../includes/proxy.php?url=${encodeURIComponent(jogo.game_img)}" alt="${jogo.game_name}" class="jogo-img" onerror="this.onerror=null; this.src='../../imagens/imageoff.png';">
          <div class="jogo-overlay">
            <button class="jogar-btn modalLogin"><i class="fas fa-play"></i> JOGAR</button>
          </div>
          <div class="jogo-info">
            <div class="nome">${jogo.game_name.length > 15 ? jogo.game_name.substring(0, 15) + '...' : jogo.game_name}</div>
            <div class="jogadores">
              <span class="online-dot"></span>
              <span class="online-number">0</span>
              <span class="jogadores-text">jogadores</span>
            </div>
          </div>
        `;
        container.appendChild(card);
      });

      offset += limit;

      if (window.innerWidth <= 768) {
        const scrollAmount = (120 + 12) * 3; 
        container.scrollBy({
          left: scrollAmount,
          behavior: 'smooth'
        });
      }
    });
}

carregarMais();


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


//Funções abre os termos
     function abrirSidebar(conteudoId) {
    $('#sidebar').addClass('show');

    $.ajax({
         url: 'termo/' + conteudoId + '.php',
        success: function (data) {
            $('#sidebarContent').html(data);
        },
        error: function () {
            $('#sidebarContent').html('<p>Erro ao carregar o conteúdo.</p>');
        }
    });
}

$(document).ready(function () {
    $(document).on('click', '#termo-condicao, #termo-privacidade, #termo-cookies, #termo-18anos, #termo-jogo-responsavel', function (e) {
        e.preventDefault();
        const conteudoId = $(this).attr('id');
        abrirSidebar(conteudoId);
    });

    // Fechar sidebar
    $(document).on('click', '.close-sidebar', function () {
        $('#sidebar').removeClass('show');
    });
});


});