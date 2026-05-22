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

require_once '../includes/db.php';

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    // Sanitiza e valida os dados de entrada para evitar ataques como XSS
    $data = array(
        "cpf"      => htmlspecialchars(trim(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'), 
        "senha" => trim(filter_input(INPUT_POST, "senha", FILTER_UNSAFE_RAW))
    );

    // Validações
    if (!valida_token_csrf('login')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["cpf"])) {
        $errors[] = "O campo CPF é obrigatório!";
    } else if (strlen($data["cpf"]) < 14) {
        $errors[] = "O campo CPF está incompleto!";
    } else if (empty($errors) && empty($data["senha"])) {
        $errors[] = "O campo senha é obrigatório!";
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {

    
        $sql = "SELECT id, bet_senha, bet_status FROM bet_usuarios WHERE bet_cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cpf' => $data['cpf']]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            if ($usuario['bet_status'] != 1) {
                $errors[] = "Conta suspensa para análise!";
            } else if (password_verify($data['senha'], $usuario['bet_senha'])) {

                $auth_token = bin2hex(random_bytes(32));

                // Atualiza o token no banco de dados
                $updateSql = "UPDATE bet_usuarios SET bet_token = :auth_token WHERE id = :id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':auth_token' => $auth_token,
                    ':id' => $usuario['id']
                ]);

// Setar cookie com token (1 ano)
$cookie_options = [
    'expires' => time() + 31536000, // 1 ano
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'], 
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
];

setcookie('auth_token', $auth_token, $cookie_options);
$_SESSION['usuario_id'] = $usuario['id'];

        $successMessage = "Login realizado com sucesso! Aguardem...";
        $response = array(
            "status" => "alertasim",
            "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenera o token CSRF após um envio bem-sucedido
        $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32));

            }else {
                // CPF ou senha incorretos
                $errors[] = "CPF ou senha incorretos!";
            }

        }else {
            // Usuário não encontrado
            $errors[] = "Usuário não encontrado!";
        }   
    }

        if (!empty($errors)) {
        $response = array(
        "status" => "alertanao",
        "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
       );
           
    } 

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}  