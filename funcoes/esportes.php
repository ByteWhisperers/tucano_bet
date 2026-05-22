<?php 
if (!defined('IN_INDEX')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$host}/");
    exit();
}

// Pega apenas o jogo 'sport' se estiver ativo
$stmt = $pdo->prepare("SELECT game_name FROM bet_jogos WHERE game_ativado = 1 AND game_code = 'sport' LIMIT 1");
$stmt->execute();
$jogo = $stmt->fetch(PDO::FETCH_ASSOC);

if ($jogo): ?>
 <button class="botao-esporte-btn modalLogin">
    <i class="fas fa-futbol"></i> APOSTAS ESPORTIVAS
</button>

<?php endif; ?>
