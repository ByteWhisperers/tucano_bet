<?php 
if (!defined('IN_INDEX')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$host}/");
    exit();
}

$stmt = $pdo->prepare("
    SELECT id, game_code, game_name 
    FROM bet_jogos 
    WHERE game_ativado = 1 
    AND game_code = 'sport' 
    LIMIT 1
");
$stmt->execute();
$jogo = $stmt->fetch(PDO::FETCH_ASSOC);

if ($jogo):
?>
    <button class="jogar-btn botao-esporte-btn" 
        data-modal="modal-slots<?= $jogo['id']; ?>" 
        data-url="" 
        data-game-id="<?= $jogo['game_code']; ?>">
        <i class="fas fa-futbol"></i> APOSTAS ESPORTIVAS
    </button>
<?php endif; ?>
