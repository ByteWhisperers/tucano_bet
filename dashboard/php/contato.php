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
require_once '../../includes/config.php';

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    // Sanitiza e valida os dados de entrada para evitar ataques como XSS
    $data = array(
        "nome"     => htmlspecialchars(trim(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
        "email"    => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL), 
        "assunto"  => htmlspecialchars(trim(filter_input(INPUT_POST, "assunto", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
        "mensagem" => htmlspecialchars(trim(filter_input(INPUT_POST, "mensagem", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8')
    );

    // Validações
    if (!valida_token_csrf('contato')) {
    $errors[] = "Falha. Por favor, tente novamente.";
    } elseif (empty($data["nome"])) {
        $errors[] = "O campo nome é obrigatório!";
    } elseif (empty($data["email"])) {
        $errors[] = "O campo email é obrigatório!";
    } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido!";
    } elseif (empty($data["assunto"])) {
        $errors[] = "O campo assunto é obrigatório!";
    } elseif (empty($data["mensagem"])) {
        $errors[] = "O campo mensagem é obrigatório!";
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {

        // Inclui o PHPMailer
        include '../../phpmailer/PHPMailerAutoload.php';

        // Cria uma nova instância do PHPMailer
        $phpMailer = new PHPMailer();
        $phpMailer->isSMTP();
        $phpMailer->Host = $EmailHost;
        $phpMailer->SMTPAuth = true;
        $phpMailer->Username = $EmailEmail;
        $phpMailer->Password = $EmailSenha;
        $phpMailer->SMTPSecure = $EmailSMTP; 
        $phpMailer->Port = $EmailPorta;

        $phpMailer->SMTPOptions = [
            'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            ]
        ];

        // Define a codificação de caracteres
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->setFrom($EmailEmail, 'Contato via site');
        $phpMailer->addAddress($EmailEmail, $NomeSite); // Para onde o email será enviado
        $phpMailer->addReplyTo($data['email'], $data['nome']);
        $phpMailer->Subject = $data["assunto"];
        $phpMailer->isHTML(true); 

        // Monta o corpo do email
        $phpMailer->Body = "
            <h2>Nova Mensagem de Contato</h2>
            <p><strong>Nome:</strong> {$data['nome']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Assunto:</strong> {$data['assunto']}</p>
            <p><strong>Mensagem:</strong><br>{$data['mensagem']}</p>
        ";

         // Envia o email
        if (!$phpMailer->send()) {

             $response = array(
        "status" => "alertanao",
        "message" => "<p class='alertanao'>Erro ao enviar a mensagem. Tente novamente! <span><i class='fas fa-times'></i></span></p>"
          );

        } else {
           
        $successMessage = "Mensagem enviada com sucesso!";
        $response = array(
            "status" => "alertasim",
            "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenera o token CSRF após um envio bem-sucedido
        $_SESSION['csrf_token_contato'] = bin2hex(random_bytes(32));

        }     
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}