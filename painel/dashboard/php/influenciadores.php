<?php 
// Impede acesso direto via navegador (GET)
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    header("Location: /painel/dashboard/");
    exit;
}

session_name('adm_session');
session_start();

require_once '../../../includes/db.php';       // conexão PDO
require_once 'auth_ajax_adm.php';              // autenticação AJAX

header('Content-Type: application/json');

// Recebe dados do fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['field'], $data['status'])) {
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

$id = (int)$data['id'];
$field = $data['field'];
$status = (int)$data['status'];

// Só permite atualizar o campo bet_influenciador
if ($field !== 'bet_influenciador') {
    echo json_encode(['erro' => 'Campo inválido']);
    exit;
}

// Atualiza no banco
$stmt = $pdo->prepare("UPDATE bet_usuarios SET bet_influenciador = :status WHERE id = :id");
$executou = $stmt->execute([':status' => $status, ':id' => $id]);

if ($executou) {
    echo json_encode(['sucesso' => true, 'status' => $status]);
} else {
    echo json_encode(['erro' => 'Falha ao atualizar']);
}
