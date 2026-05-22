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

    $esportes = trim($_POST["esportes"] ?? '');

    if (!valida_token_csrf('esportes')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } elseif (!in_array($esportes, ['0', '1'], true)) {
        $errors[] = "Selecione um status válido para os esportes!";
    } else {
        try {
            // Inicia transação para garantir consistência
            $pdo->beginTransaction();

            // Atualiza configuração geral
            $updateConfig = $pdo->prepare("UPDATE bet_adm_config SET bet_esporte = :status LIMIT 1");
            $updateConfig->execute([
                ':status' => (int)$esportes
            ]);

            // Atualiza os jogos de código 'sport'
            $updateJogos = $pdo->prepare("UPDATE bet_jogos SET game_ativado = :status WHERE game_code = 'sport'");
            $updateJogos->execute([
                ':status' => (int)$esportes
            ]);

            // Confirma transação
            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Erro ao atualizar: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $mensagemStatus = ($esportes == '1')
            ? "Esportes ativados com sucesso! ⚽"
            : "Esportes desativados com sucesso!";

        $response = [
            "status" => "alertasim",
            "message" => "<p class='alertasim'>$mensagemStatus <span><i class='fas fa-check'></i></span></p>"
        ];

        // Regenera token CSRF
        $_SESSION['csrf_token_esportes'] = bin2hex(random_bytes(32));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}