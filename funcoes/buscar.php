<?php
if (!defined('IN_INDEX') && !( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: {$protocol}{$host}/");
    exit();
}

header('Content-Type: text/html; charset=utf-8');
require_once '../includes/db.php';

$q = isset($_POST['q']) ? trim($_POST['q']) : '';

// Validação da entrada
if ($q !== '' && preg_match('/^[\p{L}\p{N}\s]{1,100}$/u', $q)) {
    $stmt = $pdo->prepare("SELECT game_name, game_img FROM bet_jogos WHERE game_name LIKE ? AND game_ativado = 1 AND game_code != 'sport' LIMIT 10");
    $stmt->execute(["%$q%"]);

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch()) {
            echo '
    <table class="busca-tabela-resultado">
        <tr>
            <td class="col-img">
                <img src="../../includes/proxy.php?url=' . urlencode($row['game_img']) . '" alt="' . htmlspecialchars($row['game_name']) . '" class="busca-img" onerror="this.onerror=null; this.src=\'../../imagens/imageoff.png\';">
            </td>
            <td class="col-nome">
                ' . htmlspecialchars($row['game_name']) . '
            </td>
            <td class="col-jogadores">
                <span class="jogadores-online">
                    <span class="online-dot"></span><span class="online-number">0</span> Jogadores
                </span>
            </td>
            <td class="col-jogar">
                <button class="btn-jogar modalLogin">Jogar</button>
            </td>
        </tr>
    </table>';
        }
    } else {
        // Mensagem centralizada dentro de uma tabela
        echo '
    <table class="busca-tabela-resultado">
        <tr>
            <td colspan="4" class="sem-resultado">Nenhum jogo encontrado.</td>
        </tr>
    </table>';
    }
}
?>