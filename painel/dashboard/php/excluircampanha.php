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

// =========================
// CSRF
// =========================
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"])
        && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors = [];

    // Sanitização
    $campanhaId = filter_input(INPUT_POST, 'campanha_id', FILTER_VALIDATE_INT);

    // Validações
    if (!valida_token_csrf('excluircampanha')) {
        $errors[] = "Falha de segurança. Atualize a página e tente novamente.";
    } elseif (empty($campanhaId)) {
        $errors[] = "Campanha inválida ou não informada.";
    }

    if (!empty($errors)) {

        $response = [
            "status"  => "alertanao",
            "message" => "<p class='alertanao'>" .
                implode("<br>", $errors) .
                " <span><i class='fas fa-times'></i></span></p>"
        ];

    } else {

        try {
            // =========================
            // TRANSACTION
            // =========================
            $pdo->beginTransaction();

            // =========================
            // EXCLUI FILA DA CAMPANHA
            // =========================
            $stmtFila = $pdo->prepare("
                DELETE FROM bet_marketing_envios
                WHERE bet_config_id = :campanha_id
            ");
            $stmtFila->bindParam(':campanha_id', $campanhaId, PDO::PARAM_INT);
            $stmtFila->execute();

            // =========================
            // EXCLUI CAMPANHA
            // =========================
            $stmtCampanha = $pdo->prepare("
                DELETE FROM bet_marketing_config
                WHERE bet_id = :campanha_id
            ");
            $stmtCampanha->bindParam(':campanha_id', $campanhaId, PDO::PARAM_INT);
            $stmtCampanha->execute();

            // =========================
            // COMMIT
            // =========================
            $pdo->commit();

            // Regenera token CSRF
            $_SESSION['csrf_token_excluircampanha'] = bin2hex(random_bytes(32));

            $response = [
                "status"  => "alertasim",
                "message" => "<p class='alertasim'>
                    Campanha excluída com sucesso!
                    <span><i class='fas fa-check'></i></span>
                </p>"
            ];

        } catch (PDOException $e) {

            $pdo->rollBack();

            $response = [
                "status"  => "alertanao",
                "message" => "<p class='alertanao'>
                    Erro ao excluir campanha.
                    <span><i class='fas fa-times'></i></span>
                </p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
