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

    // Sanitiza e valida os dados de entrada
    $data = array(
        "senha" => trim(filter_input(INPUT_POST, "senha", FILTER_UNSAFE_RAW)),
        "confirmasenha" => trim(filter_input(INPUT_POST, "confirmasenha", FILTER_UNSAFE_RAW))
    );

    // Validações
    if (!valida_token_csrf('senha')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["senha"])) {
        $errors[] = "O campo senha é obrigatório!";
    } else if (strlen($data["senha"]) < 8) {
        $errors[] = "A senha deve ter no mínimo 8 caracteres!";
    } else if (empty($data["confirmasenha"])) {
        $errors[] = "O campo confirmar senha é obrigatório!";
    } else if ($data["senha"] != $data["confirmasenha"]) {
        $errors[] = "Confirmação de senha não confere!";
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {
        try {
            // Prepara a consulta de atualização
            $sql = "UPDATE bet_usuarios SET bet_senha = :senha WHERE id = :usuario_id";
            $stmt = $pdo->prepare($sql);

            $usuario_id = $_SESSION['usuario_id'];
            $hashed_password = password_hash($data["senha"], PASSWORD_DEFAULT);

            // Bind dos parâmetros
            $stmt->bindParam(':senha', $hashed_password);
            $stmt->bindParam(':usuario_id', $usuario_id);

            // Executa a atualização
            $stmt->execute();

            $successMessage = "Senha atualizada com sucesso!";
            $response = array(
                "status" => "alertasim",
                "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
            );

            // Regenera o token CSRF após um envio bem-sucedido
            $_SESSION['csrf_token_senha'] = bin2hex(random_bytes(32));
        } catch (PDOException $e) {
            $response = array(
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro ao atualizar senha. Tente novamente. <span><i class='fas fa-times'></i></span></p>"
            );
        }
    }

    // Envia a resposta em formato JSON e encerra o script
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>