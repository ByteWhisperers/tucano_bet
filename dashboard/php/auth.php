<?php
// Se não estiver logado e tiver o cookie com token
if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];

    $stmt = $pdo->prepare("SELECT id, bet_status FROM bet_usuarios WHERE bet_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['bet_status'] == 1) {
        $_SESSION['usuario_id'] = $usuario['id'];
    } else {
        setcookie("auth_token", "", time() - 3600, "/");
        header("Location: /");
        exit;
    }
}

// Se ainda não estiver logado, redireciona
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /");
    exit;
}

// Puxar os dados do usuário logado
$stmt = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $saldo = $usuario['bet_saldo'];
}
?>