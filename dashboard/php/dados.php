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
    $errors = array();

    // Sanitiza e valida os dados de entrada para evitar ataques como XSS
    $data = array(
        "email"    => filter_input(INPUT_POST, "emaildados", FILTER_SANITIZE_EMAIL)
    );

    // Validações
    if (!valida_token_csrf('dados')) {
    $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["email"])) {
    $errors[] = "O campo email é obrigatório!";
    } else if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email inválido!";
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {
        
        try {

            $usuario_id = $_SESSION['usuario_id'];

            // Verifica se o e-mail já pertence a outro usuário
            $stmt = $pdo->prepare("SELECT id FROM bet_usuarios WHERE bet_email = :email");
            $stmt->bindParam(':email', $data["email"]);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['id'] != $usuario_id) {
                $response = array(
                    "status" => "alertanao",
                    "message" => "<p class='alertanao'>Este e-mail já está em uso! <span><i class='fas fa-times'></i></span></p>"
                );
            }else {
                // Atualiza o e-mail
                $stmt = $pdo->prepare("UPDATE bet_usuarios SET bet_email = :email WHERE id = :usuario_id");
                $stmt->bindParam(':email', $data["email"]);
                $stmt->bindParam(':usuario_id', $usuario_id);
                $stmt->execute();

                $response = array(
                    "status" => "alertasim",
                    "message" => "<p class='alertasim'>Dados atualizado com sucesso! <span><i class='fas fa-check'></i></span></p>"
                );

                // Regenera o token CSRF após um envio bem-sucedido
                $_SESSION['csrf_token_dados'] = bin2hex(random_bytes(32));

            }
        } catch (PDOException $e) {
            $response = array(
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro ao atualizar dados. Tente novamente. <span><i class='fas fa-times'></i></span></p>"
            );
        }   
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}