<?php
if (!defined('IN_INDEX')) {
    header("Location: /dashboard/");
    exit();
}
?>
<div class="container-conteudo">

<!-- Jogos da semana -->
<div class="lista-jogos">
    <span class="titulo-lista-jogos">🔥 Jogados Da Semana</span>
    <div class="jogos-container">
        <?php include 'funcoes/jogo-semana.php'; ?>
    </div>
</div>

<!-- Jogos da PRAGMATIC -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> PRAGMATIC</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-pragmatic.php'; ?>
    </div>
</div>

<!-- Jogos da PGSOFT -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> PGSOFT</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-pgsoft.php'; ?>
    </div>
</div>

<!-- Jogos da SPRIBE -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> SPRIBE</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-spribe.php'; ?>
    </div>
</div>

<!-- Jogos todos -->
<div class="lista-jogos">
    <span class="titulo-lista-jogos"> Todos os jogos</span> 
  <div id="jogosCarregaveis" class="jogos-container"></div>
  <button id="verMaisBtn" class="btn-ver-mais">Ver mais</button>
</div>

<div class="footer-line"></div>

</div>

<script>
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
          <img src="../../includes/proxy.php?url=${encodeURIComponent(jogo.game_img)}" alt="${jogo.game_name}" class="jogo-img" class="jogo-img" onerror="this.onerror=null; this.src='../../../imagens/imageoff.png';">
          <div class="jogo-overlay">
            <button class="jogar-btn" data-modal="modal-slots${jogo.id}" data-url="" data-game-id="${jogo.game_code}"><i class="fas fa-play"></i> JOGAR</button>
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
</script>