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
        "host"  => trim($_POST["host"] ?? ''),
        "email" => trim($_POST["email"] ?? ''),
        "senha" => trim($_POST["senha"] ?? ''),
        "porta" => trim($_POST["porta"] ?? ''),
        "smtp"  => trim($_POST["smtp"] ?? '')
    ];

    if (!valida_token_csrf('email')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["host"])) {
            $errors[] = "O campo Servidor (SMTP) é obrigatório!";
    } else if (empty($data["email"])) {
            $errors[] = "O campo E-mail é obrigatório!";
    } else if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "O E-mail informado não é válido!";
    } else if (empty($data["senha"])) {
            $errors[] = "O campo Senha é obrigatório!";
    } else if (empty($data["porta"])) {
            $errors[] = "Selecione a Porta SMTP!";
    } else if (!in_array($data["porta"], ['25', '465', '587'])) {
            $errors[] = "Porta SMTP inválida!";
    } else if (empty($data["smtp"])) {
            $errors[] = "Selecione o Tipo de Segurança!";
    } else if (!in_array($data["smtp"], ['ssl', 'tls'])) {
            $errors[] = "Tipo de Segurança inválido!";
    }
    
    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET 
                bet_email_host = :host, 
                bet_email_email = :email, 
                bet_email_senha = :senha, 
                bet_email_porta = :porta, 
                bet_email_smtp = :smtp 
                WHERE id = 1");
            $stmt->bindParam(':host', $data['host']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':senha', $data['senha']);
            $stmt->bindParam(':porta', $data['porta']);
            $stmt->bindParam(':smtp', $data['smtp']);
            $stmt->execute();

            $_SESSION['csrf_token_email'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Configurações atualizadas com sucesso! <span><i class='fas fa-check'></i></span></p>"
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
?>