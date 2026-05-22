<?php
require_once '../../includes/db.php';
require_once '../../includes/config.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Verificar token de autenticação
$headers = getallheaders();
$authorization = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');

if (!$authorization || $authorization !== 'Bearer ' . $TokenGeraPix) {
    http_response_code(401);
    exit;
}

// Ler JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar parâmetros obrigatórios
if (!isset($data['id_transacao'], $data['status'])) {
    http_response_code(400);
    exit;
}

$id_transacao = $data['id_transacao'];
$status = $data['status'];

// Atualizar status se for Aprovado
if ($status === 'Aprovado') {
    $update = $pdo->prepare("UPDATE bet_transacoes SET bet_status = 'Aprovado' WHERE bet_id_transacao = :id_transacao");
    $update->execute([':id_transacao' => $id_transacao]);
}

// Resposta de sucesso
http_response_code(200);
echo json_encode(['status' => 'success']);
exit;