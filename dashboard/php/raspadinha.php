<?php
// Impede acesso direto via navegador (GET)
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    header("Location: /");
    exit;
}

session_start();

require_once '../../includes/db.php';

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Sanitiza o valor recebido
    $bonus = filter_input(INPUT_POST, 'bonus', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (!valida_token_csrf('raspadinha')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($bonus) || $bonus <= 0) {
        $errors[] = "Valor de bônus inválido!";
    }

    if (!isset($_SESSION['usuario_id'])) {
        $errors[] = "Usuário não autenticado!";
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $usuario_id = $_SESSION['usuario_id'];

            // Insere o bônus na tabela
            $stmt = $pdo->prepare("INSERT INTO bet_bonus (bet_usuario, bet_bonus_tipo, bet_bonus_valor, bet_bonus_status, bet_data) VALUES (:usuario_id, 'Raspadinha', :bonus, 0, NOW())");
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':bonus', $bonus);
            $stmt->execute();

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Bônus resgatado com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];

            // Gera novo token CSRF após sucesso
            $_SESSION['csrf_token_raspadinha'] = bin2hex(random_bytes(32));

        } catch (PDOException $e) {
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro ao registrar o bônus. Tente novamente. <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
