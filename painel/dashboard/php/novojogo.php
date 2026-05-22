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

    // Sanitiza e coleta os dados
    $data = [
        'game_name'     => htmlspecialchars(trim($_POST['game_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'game_code'     => htmlspecialchars(trim($_POST['game_code'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'game_provider' => htmlspecialchars(trim($_POST['game_provider'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'game_img'      => htmlspecialchars(trim($_POST['game_img'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'game_type'     => htmlspecialchars(trim($_POST['game_type'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'game_original' => trim($_POST['game_original'] ?? ''),
    ];

    // Validações
if (!valida_token_csrf('jogos')) {
    $errors[] = "Falha. Por favor, tente novamente.";
} else if (empty($data['game_name'])) {
    $errors[] = "O nome do jogo é obrigatório!";
} else if (empty($data['game_code'])) {
    $errors[] = "O código do jogo é obrigatório!";
} else if (empty($data['game_provider'])) {
    $errors[] = "O provedor do jogo é obrigatório!";
} else if (empty($data['game_img'])) {
    $errors[] = "A URL da imagem do jogo é obrigatória!";
} else if (!filter_var($data['game_img'], FILTER_VALIDATE_URL)) {
    $errors[] = "A URL da imagem do jogo deve ser válida!";
} else if (empty($data['game_type'])) {
    $errors[] = "O tipo do jogo é obrigatório!";
} else if ($data['game_original'] !== '1' && $data['game_original'] !== '0') {
    $errors[] = "Selecione se o jogo é Original ou Clone!";
}

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO bet_jogos 
    (game_name, game_code, game_provider, game_img, game_type, game_original, game_status, game_ativado) 
    VALUES (:game_name, :game_code, :game_provider, :game_img, :game_type, :game_original, 1, 1)");

            $stmt->bindParam(':game_name',     $data['game_name']);
            $stmt->bindParam(':game_code',     $data['game_code']);
            $stmt->bindParam(':game_provider', $data['game_provider']);
            $stmt->bindParam(':game_img',      $data['game_img']);
            $stmt->bindParam(':game_type',     $data['game_type']);
            $stmt->bindParam(':game_original', $data['game_original'], PDO::PARAM_INT);

            $stmt->execute();

            // Regenera o token CSRF após inserção
            $_SESSION['csrf_token_jogos'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Jogo adicionado com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];
        } catch (PDOException $e) {
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro ao inserir: " . $e->getMessage() . " <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}