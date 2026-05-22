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

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Sanitiza e valida a cor recebida
    $cor = htmlspecialchars(trim($_POST['cor'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Validação do CSRF
    if (!valida_token_csrf('cores')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } elseif (empty($cor)) {
        $errors[] = "Selecione uma cor!";
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_cor = :cor WHERE id = 1");
            $stmt->bindParam(':cor', $cor);
            $stmt->execute();

            // Regenera o token CSRF
            $_SESSION['csrf_token_cores'] = bin2hex(random_bytes(32));

            $successMessage = "Cor atualizada com sucesso!";
            $response = [
                "status"  => "alertasim",
                "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
            ];
        } catch (PDOException $e) {
            $response = [
                "status" => "alertnao",
                "message" => "<p class='alertnao'>Erro ao atualizar: " . $e->getMessage() . " <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}