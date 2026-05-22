<?php
// Inclua seu arquivo de conexão ao banco de dados
session_start();

require_once '../../includes/db.php';

// Impede acesso direto via navegador (GET não AJAX)
if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
) {
    header("Location: /dashboard/");
    exit;
}

// Captura o txid da URL
$txid = isset($_GET['txid']) ? $_GET['txid'] : '';

// Resposta padrão
$response = ['status' => 'error'];

// Verifica se o txid não está vazio
if (!empty($txid)) {
    // Execute uma consulta ao banco de dados para verificar o status do pagamento
    $query = $pdo->prepare("SELECT bet_status FROM bet_transacoes WHERE bet_id_transacao = :txid");
    $query->bindParam(':txid', $txid);
    $query->execute();
    $pagamento = $query->fetch(PDO::FETCH_ASSOC);

    // Verifique se o pagamento foi encontrado
    if ($pagamento) {
        $response['status'] = $pagamento['bet_status']; // Retorna apenas o status
    }
}

// Retorne a resposta em formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>