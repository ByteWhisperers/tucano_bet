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
    $errors = array();

    // Sanitiza e valida os dados de entrada para evitar ataques como XSS
    $data = array(
        "nomesite" => htmlspecialchars(trim(filter_input(INPUT_POST, "nomesite", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
        "urlsite" => trim($_POST["urlsite"] ?? '')
    );

    // Validações
if (!valida_token_csrf('nomeurl')) {
    $errors[] = "Falha. Por favor, tente novamente.";
} elseif (empty($data["nomesite"])) {
    $errors[] = "O campo nome do site é obrigatório!";
} elseif (empty($data["urlsite"])) {
    $errors[] = "O campo URL do site é obrigatório!";
} elseif (!filter_var($data["urlsite"], FILTER_VALIDATE_URL)) {
    $errors[] = "O campo URL do site não é válido!";
} else {
    // Verifica se começa com https://
    $parsed = parse_url($data["urlsite"]);
    if (!isset($parsed['scheme']) || strtolower($parsed['scheme']) !== 'https') {
        $errors[] = "A URL deve começar com https://";
    }
}

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {
        
        try {
           $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_site_nome = :nomesite, bet_site_url = :urlsite WHERE id = 1");
            $stmt->bindParam(':nomesite', $data['nomesite']);
            $stmt->bindParam(':urlsite', $data['urlsite']);
            $stmt->execute();

            // Regenera o token CSRF após um envio bem-sucedido
            $_SESSION['csrf_token_nomeurl'] = bin2hex(random_bytes(32));

            $successMessage = "Nome e URL atualizada com sucesso!";
            $response = array(
                "status"  => "alertasim",
                "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
            );
        } catch (PDOException $e) {
            $response = array(
                "status" => "alertnao",
                "message" => "<p class='alertnao'>Erro ao atualizar: " . $e->getMessage() . " <span><i class='fas fa-times'></i></span></p>"
            );
        }
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}