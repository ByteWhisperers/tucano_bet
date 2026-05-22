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
        "instagram" => trim($_POST["instagram"] ?? ''),
        "telegram"  => trim($_POST["telegram"] ?? '')
    ];

    if (!valida_token_csrf('redes')) {
    $errors[] = "Falha. Por favor, tente novamente.";
} else {
    if (empty($data["instagram"])) {
        $errors[] = "O campo Instagram é obrigatório!";
    } elseif (!filter_var($data["instagram"], FILTER_VALIDATE_URL)) {
        $errors[] = "O link do Instagram não é válido!";
    }else

    if (empty($data["telegram"])) {
        $errors[] = "O campo Telegram é obrigatório!";
    } elseif (!filter_var($data["telegram"], FILTER_VALIDATE_URL)) {
        $errors[] = "O link do Telegram não é válido!";
    }
}

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_instagram = :instagram, bet_telegram = :telegram WHERE id = 1");
            $stmt->bindParam(':instagram', $data['instagram']);
            $stmt->bindParam(':telegram', $data['telegram']);
            $stmt->execute();

            $_SESSION['csrf_token_redes'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Redes sociais atualizadas com sucesso! <span><i class='fas fa-check'></i></span></p>"
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