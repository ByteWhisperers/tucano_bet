<?php
// Impede acesso direto via navegador (GET)
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    header("Location: /painel/dashboard/");
    exit();
}

session_name('adm_session');
session_start();

require_once '../../../includes/db.php';
require_once '../../../includes/config.php';

// Autenticação AJAX
require_once 'auth_ajax_adm.php';

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Sanitiza e valida os dados de entrada
    $extrato_id = isset($_POST['extrato_id']) ? (int)$_POST['extrato_id'] : 0; 

    if (!valida_token_csrf('cancelar')) {
        $errors[] = "Falha na validação do token. Por favor, tente novamente.";
    } else {
        if ($extrato_id <= 0) {
            $errors[] = "ID do extrato inválido!";
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        // Atualiza o status da transação para Cancelado
        $update = $pdo->prepare("UPDATE bet_transacoes SET bet_status = 'Cancelado' WHERE id = :id");
        $executed = $update->execute([':id' => $extrato_id]);

        if ($executed) {
            // Gera novo token CSRF para o cancelamento
            $_SESSION['csrf_token_cancelar'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Transação cancelada com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];
        } else {
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro ao cancelar a transação. Tente novamente.<span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
