<?php
if (!defined('IN_INDEX')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: /");
    exit();
}

// Busca 50 jogos aleatórios da tabela
$stmt = $pdo->query("SELECT game_name, game_img FROM bet_jogos WHERE game_ativado = 1 ORDER BY RAND() LIMIT 50");
$nomes = ["Ana", "Carlos", "Bruna", "Marcos", "Juliana", "Gabriel", "Fernanda", "Pedro", "Camila", "Rafael", "Lucas", "Larissa", "Thiago", "Isabela", "João", "Patrícia", "André", "Renata", "Felipe", "Beatriz"];
$sobrenomes = ["S", "R", "L", "F", "C", "A", "M", "D", "G", "T"];

$ganhadores = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Gera nome aleatório com privacidade
    $nome = $nomes[array_rand($nomes)] . ' ' . $sobrenomes[array_rand($sobrenomes)] . '****';
    $ganhadores[] = [
        'game_img' => $row['game_img'],
        'game_name' => $row['game_name'],
        'nome' => $nome,
    ];
}

// Função para gerar valor aleatório no range 20-900
function valor_aleatorio() {
    return number_format(rand(2000, 90000) / 100, 2, ',', '.');
}

// Exibe os cards duplicados para loop contínuo
for ($loop = 0; $loop < 2; $loop++) {
    foreach ($ganhadores as $item) {
        ?>
        <div class="card">
            <img src="../../includes/proxy.php?url=<?= urlencode($item['game_img']) ?>" alt="<?= htmlspecialchars($item['game_name']) ?>" onerror="this.onerror=null; this.src='../../../imagens/imageoff.png';">
            <div>
                <strong><?= htmlspecialchars($item['nome']) ?></strong><br>
                <?= mb_strimwidth(htmlspecialchars($item['game_name']), 0, 15, '...') ?><br>
                <span class="valor">R$ <?= valor_aleatorio() ?></span>
            </div>
        </div>
        <?php
    }
}
?>