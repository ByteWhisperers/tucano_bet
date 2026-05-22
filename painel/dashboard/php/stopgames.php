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
require_once 'auth_ajax_adm.php'; // Autenticação AJAX

// Função para validar CSRF
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = [];

    $stopgames = trim($_POST["stopgames"] ?? '');

    if (!valida_token_csrf('stopgames')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } elseif (!in_array($stopgames, ['0', '1'], true)) {
        $errors[] = "Selecione um status válido para os jogos!";
    } else {
        try {
            // Atualiza a tabela de configuração geral
            $update = $pdo->prepare("UPDATE bet_adm_config SET bet_stop_games = :status LIMIT 1");
            $update->execute([
                ':status' => (int)$stopgames
            ]);
        } catch (PDOException $e) {
            $errors[] = "Erro ao atualizar: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $response = [
            "status" => "alertasim",
            "message" => "<p class='alertasim'>Status dos jogos atualizado com sucesso! <span><i class='fas fa-check'></i></span></p>"
        ];

        // Regenera token CSRF
        $_SESSION['csrf_token_stopgames'] = bin2hex(random_bytes(32));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}