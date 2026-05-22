<?php
if (!defined('IN_INDEX')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$host}/");
    exit();
}

$sql = "
    SELECT 
        MIN(game_name) AS game_name, 
        MIN(game_img) AS game_img 
    FROM bet_jogos 
    WHERE game_ativado = 1 
    GROUP BY game_code 
    ORDER BY RAND()
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$jogos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nomes = [
  "Fernando Silva", "Ana Pereira", "Matheus Pedro", "Carla Souza", "Lucas Lima", "Bruno Costa", "Larissa Mota",
  "Mariana Oliveira", "Thiago Santos", "Beatriz Rocha", "Rafael Alves", "Camila Ferreira", "Gustavo Martins",
  "Juliana Dias", "Pedro Henrique", "Larissa Carvalho", "Eduardo Ramos", "Amanda Castro", "Felipe Moreira",
  "Isabela Menezes", "Vitor Almeida", "Sofia Correia", "Diego Nunes", "Luana Pires", "Carlos Eduardo",
  "Renata Borges", "João Paulo", "Patrícia Monteiro", "Ricardo Lima", "Marcos Vinicius", "Gabriela Fernandes",
  "Bruna Lopes", "Caio Souza", "Tatiana Mello", "Alexandre Silva", "Daniela Costa", "Leandro Barbosa",
  "Mônica Alves", "Rodrigo Freitas", "Priscila Moreira", "André Oliveira", "Fernanda Gomes", "Lucas Andrade"
];
?>

<script>
  const jogos = <?php echo json_encode($jogos); ?>;
  const nomes = <?php echo json_encode($nomes); ?>;

  function gerarAposta() {
    const jogo = jogos[Math.floor(Math.random() * jogos.length)];
    const nomeCompleto = nomes[Math.floor(Math.random() * nomes.length)];
    const partesNome = nomeCompleto.split(" ");
    const nomeMascara = partesNome[0] + " " + (partesNome[1] ? partesNome[1].slice(0, 2) + "****" : "");

    // 85% aposta entre 0.40 e 10, 15% entre 10 e 50
    let aposta;
    if (Math.random() < 0.85) {
      aposta = Math.random() * (10 - 0.4) + 0.4;
    } else {
      aposta = Math.random() * (50 - 10) + 10;
    }

    // Definir se aposta terá valor inteiro ou decimal
    // 90% chances de inteiro, 10% decimal
    if (Math.random() < 0.9) {
      aposta = Math.floor(aposta);
      if (aposta < 1) aposta = 1; // evita zero
    } else {
      aposta = aposta.toFixed(2);
    }

    const multiplicador = Math.floor(Math.random() * 49) + 1;
    const ganho = (aposta * multiplicador).toFixed(2);

    const html = `
      <div class="aovivo-entry">
        <img src="../../includes/proxy.php?url=${encodeURIComponent(jogo.game_img)}" alt="${jogo.game_name}" onerror="this.onerror=null; this.src='../../imagens/imageoff.png';" />
        <div class="aovivo-info">
          <strong>${nomeMascara}</strong> Apostou: R$ ${aposta}<br>
          ${multiplicador}x <span class="aovivo-amount">R$ ${ganho}</span>
        </div>
      </div>
    `;

    const container = document.getElementById("aovivo-container");
    container.insertAdjacentHTML("afterbegin", html);

    while (container.children.length > 4) {
      container.removeChild(container.lastChild);
    }
  }

  for(let i = 0; i < 4; i++) {
    gerarAposta();
  }

  setInterval(gerarAposta, 2000);
</script>