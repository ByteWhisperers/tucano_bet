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
require_once '../includes/config.php';

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
    );

    // Validações
    if (!valida_token_csrf('recuperar_senha')) {
    $errors[] = "Falha. Por favor, tente novamente.";
    } if (empty($data["cpf"])) {
        $errors[] = "O campo CPF é obrigatório!";
    } else if (strlen($data["cpf"]) < 14) {
        $errors[] = "O campo CPF está incompleto!";
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {

        // Consulta SQL para verificar se o CPF existe e buscar o email correspondente
        $sql = "SELECT bet_email FROM bet_usuarios WHERE bet_cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cpf', $data['cpf']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $novaSenha = bin2hex(random_bytes(4));

        // Atualizar a nova senha no banco de dados
        $sqlUpdate = "UPDATE bet_usuarios SET bet_senha = :senha WHERE bet_cpf = :cpf";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':senha', password_hash($novaSenha, PASSWORD_DEFAULT));
        $stmtUpdate->bindParam(':cpf', $data['cpf']);
        $stmtUpdate->execute();
        
        // Envio do e-mail com a nova senha
        include '../phpmailer/PHPMailerAutoload.php';

            $phpMailer = new PHPMailer();
            $phpMailer->isSMTP();
            $phpMailer->Host       = $EmailHost;
            $phpMailer->SMTPAuth   = true;
            $phpMailer->Username   = $EmailEmail;
            $phpMailer->Password   = $EmailSenha;
            $phpMailer->SMTPSecure = $EmailSMTP;
            $phpMailer->Port       = $EmailPorta;

            $phpMailer->SMTPOptions = [
                'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                ]
            ];

            $phpMailer->CharSet = 'UTF-8';
            $phpMailer->setFrom($EmailEmail, $NomeSite);
            $phpMailer->addAddress($user['bet_email']); // Enviar para o email encontrado
            $phpMailer->Subject = 'Sua nova senha de acesso ao site ' . $NomeSite;
            $phpMailer->isHTML(true);

            // Corpo do email com a nova senha
            $phpMailer->Body = "
                <h2>Sua nova senha de acesso</h2>
                <p>Segue sua nova senha para acessar o site {$NomeSite}:</p>
                <p><strong>Nova Senha:</strong> {$novaSenha}</p>
                <p><a href='https://{$Site}'>Clique aqui para acessar o site</a></p>
                <p>Att, Equipe {$NomeSite}</p>
            ";

             // Envia o e-mail
            if (!$phpMailer->send()) {

                $errors[] = "Email não pôde ser enviada!";
            $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
             );

            }else {

              $successMessage = "Nova senha enviada para o email!";
              $response = array(
                    "status" => "alertasim",
                    "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
              );

            // Regenera o token CSRF após um envio bem-sucedido
            $_SESSION['csrf_token_recuperar_senha'] = bin2hex(random_bytes(32));  

            }

        } else {
            $errors[] = "CPF não encontrado!";

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