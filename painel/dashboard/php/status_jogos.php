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

require_once '../../../includes/db.php';

// Autenticação AJAX
require_once 'auth_ajax_adm.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$field = $_POST['field'] ?? '';
$value = isset($_POST['value']) ? (int) $_POST['value'] : 0;

// Lista de campos permitidos para atualizar
$campos_permitidos = ['game_ativado', 'game_destacado'];

if (!in_array($field, $campos_permitidos)) {
    http_response_code(400);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE bet_jogos SET $field = :value WHERE id = :id");
    $stmt->bindParam(':value', $value, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    http_response_code(204); // No Content
} catch (PDOException $e) {
    http_response_code(500);
    exit;
}