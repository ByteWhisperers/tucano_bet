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
    $celular = trim($_POST["celular"] ?? '');

    // Valida CSRF
    if (!valida_token_csrf('campocel')) {
        $errors[] = "Falha de segurança. Tente novamente.";
    }
    // Valida valor
    elseif (!in_array($celular, ['0', '1'], true)) {
        $errors[] = "Selecione um status válido para o campo celular!";
    }
    else {
        try {
            // Inicia transação
            $pdo->beginTransaction();

            // Atualiza configuração do campo celular
            $stmt = $pdo->prepare("
                UPDATE bet_adm_config 
                SET bet_campocel = :status 
                LIMIT 1
            ");
            $stmt->execute([
                ':status' => (int)$celular
            ]);

            // Confirma
            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Erro ao atualizar: " . $e->getMessage();
        }
    }

    // Retorno AJAX
    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {

        $mensagemStatus = ($celular == '1')
            ? "Campo celular ativado com sucesso! 📱"
            : "Campo celular desativado com sucesso!";

        $response = [
            "status" => "alertasim",
            "message" => "<p class='alertasim'>$mensagemStatus <span><i class='fas fa-check'></i></span></p>"
        ];

        // Regenera token CSRF
        $_SESSION['csrf_token_campocel'] = bin2hex(random_bytes(32));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
