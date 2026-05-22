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

    // Sanitiza e valida os dados de entrada
    $data = [
        "porcentagem_afiliados" => trim($_POST["porcentagem_afiliados"] ?? '')
    ];

    if (!valida_token_csrf('afiliados')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else {
        if ($data["porcentagem_afiliados"] === '') {
            $errors[] = "Selecione o valor da porcentagem!";
        } elseif (!is_numeric($data["porcentagem_afiliados"])) {
            $errors[] = "A porcentagem deve ser um número!";
        } else {
            $percent = (int)$data["porcentagem_afiliados"];
            if ($percent < 5 || $percent > 75) {
                $errors[] = "A porcentagem deve estar entre 5% e 75%!";
            }
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE bet_usuarios SET bet_afiliado_por = :porcentagem");
            $stmt->bindParam(':porcentagem', $data['porcentagem_afiliados'], PDO::PARAM_INT);
            $stmt->execute();

            // Gera novo token CSRF
            $_SESSION['csrf_token_afiliados'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Atualizada com sucesso para todos os usuários! <span><i class='fas fa-check'></i></span></p>"
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