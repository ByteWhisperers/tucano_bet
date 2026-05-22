<?php
if (!defined('IN_INDEX')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$host}/");
    exit();
}

//$gameCodes = ['aviator', 'mines'];
//$placeholders = implode(',', array_fill(0, count($gameCodes), '?'));

$stmt = $pdo->prepare("SELECT game_name, game_img FROM bet_jogos WHERE game_ativado = 1 AND game_provider IN ('PRAGMATIC', 'PRAGMATIC PLAY OFICIAL', 'PRAGMATIC LIVE OFICIAL', 'OFICIAL - PRAGMATIC PLAY') AND game_destacado = 1 ORDER BY RAND() LIMIT 6");
$stmt->execute();
$jogos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($jogos as $jogo): ?>
  <div class="jogo-card">
    <img src="../../includes/proxy.php?url=<?= urlencode($jogo['game_img']) ?>" alt="<?= htmlspecialchars($jogo['game_name']) ?>" class="jogo-img" onerror="this.onerror=null; this.src='../../imagens/imageoff.png';">
    <div class="jogo-overlay">
      <button class="jogar-btn modalLogin"><i class="fas fa-play"></i> JOGAR</button>
    </div>
    <div class="jogo-info">
      <div class="nome"><?= htmlspecialchars(mb_strimwidth($jogo['game_name'], 0, 15, '...')) ?></div>
      <div class="jogadores">
        <span class="online-dot"></span>
        <span class="online-number">0</span>
        <span class="jogadores-text">jogadores</span>
      </div>
    </div>
  </div>
<?php endforeach; ?>