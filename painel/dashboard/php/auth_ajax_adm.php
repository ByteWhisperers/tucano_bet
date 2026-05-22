<?php
// Se não está logado mas tem cookie, tenta autenticar via token
if (!isset($_SESSION['adm_id']) && isset($_COOKIE['auth_token_adm'])) {
    $token = $_COOKIE['auth_token_adm'];

    $stmt = $pdo->prepare("SELECT id, adm_status FROM bet_adm WHERE adm_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['adm_status'] == 1) {
        $_SESSION['adm_id'] = $usuario['id'];
    } else {
        // Apaga o cookie inválido e retorna erro JSON
        setcookie("auth_token_adm", "", time() - 3600, "/painel", "", true, true);
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida.']);
        exit;
    }
}

// Se ainda não está logado
if (!isset($_SESSION['adm_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado.']);
    exit;
}

// Busca os dados do admin logado
$stmt = $pdo->prepare("SELECT id, adm_status FROM bet_adm WHERE id = ?");
$stmt->execute([$_SESSION['adm_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['adm_status'] != 1) {
    // Logout forçado
    setcookie("auth_token_adm", "", time() - 3600, "/painel", "", true, true);
    session_destroy();
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Conta desativada.']);
    exit;
}
?>