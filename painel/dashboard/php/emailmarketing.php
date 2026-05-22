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
    $data = [
        "nomecampanha" => htmlspecialchars(trim($_POST['nomecampanha'] ?? ''), ENT_QUOTES, 'UTF-8'),
        "assuntoemail" => htmlspecialchars(trim($_POST['assuntoemail'] ?? ''), ENT_QUOTES, 'UTF-8'),
        // mensagem pode conter HTML
        "mensagem"     => trim($_POST['mensagem'] ?? '')
    ];

    // Validações
    if (!valida_token_csrf('emailmarketing')) {
        $errors[] = "Falha de segurança. Atualize a página e tente novamente.";
    } elseif (empty($data['nomecampanha'])) {
        $errors[] = "O nome da campanha é obrigatório!";
    } elseif (empty($data['assuntoemail'])) {
        $errors[] = "O assunto do e-mail é obrigatório!";
    } elseif (empty($data['mensagem'])) {
        $errors[] = "A mensagem do e-mail é obrigatória!";
    }

    if (!empty($errors)) {

        $response = [
            "status"  => "alertanao",
            "message" => "<p class='alertanao'>" 
                . implode("<br>", $errors) 
                . " <span><i class='fas fa-times'></i></span></p>"
        ];

    } else {

        try {
            // =========================
            // TRANSACTION
            // =========================
            $pdo->beginTransaction();

            // =========================
            // CRIA CAMPANHA
            // =========================
            $stmt = $pdo->prepare("
                INSERT INTO bet_marketing_config 
                (
                    bet_nome_campanha,
                    bet_assunto,
                    bet_mensagem,
                    bet_ativa,
                    bet_criada_em
                ) VALUES (
                    :nomecampanha,
                    :assunto,
                    :mensagem,
                    1,
                    NOW()
                )
            ");

            $stmt->bindParam(':nomecampanha', $data['nomecampanha']);
            $stmt->bindParam(':assunto', $data['assuntoemail']);
            $stmt->bindParam(':mensagem', $data['mensagem']);
            $stmt->execute();

            $campanhaId = $pdo->lastInsertId();

            // =========================
            // GERA FILA DE ENVIO
            // =========================
            $stmtFila = $pdo->prepare("
                INSERT INTO bet_marketing_envios
                (
                    bet_usuario_id,
                    bet_config_id,
                    bet_status,
                    bet_tentativas,
                    bet_data_envio
                )
                SELECT 
                    u.id,
                    :campanha_id,
                    'pendente',
                    0,
                    NULL
                FROM bet_usuarios u
            ");

            $stmtFila->bindParam(':campanha_id', $campanhaId, PDO::PARAM_INT);
            $stmtFila->execute();

            // =========================
            // COMMIT
            // =========================
            $pdo->commit();

            // Regenera token CSRF
            $_SESSION['csrf_token_emailmarketing'] = bin2hex(random_bytes(32));

            $response = [
                "status"  => "alertasim",
                "message" => "<p class='alertasim'>
                    Campanha criada com sucesso!
                    <span><i class='fas fa-check'></i></span>
                </p>"
            ];

        } catch (PDOException $e) {

            $pdo->rollBack();

            $response = [
                "status"  => "alertanao",
                "message" => "<p class='alertanao'>
                    Erro ao salvar campanha.
                    <span><i class='fas fa-times'></i></span>
                </p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
