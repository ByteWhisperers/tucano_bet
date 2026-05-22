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

// Função para validar CSRF
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    $data = array(
        "usuario_id" => (int)($_POST["usuario_id"] ?? 0),
        "porcentagem_afiliado" => trim($_POST["porcentagem_afiliado"] ?? '')
    );

    if (!valida_token_csrf('porcentagemafiliado')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["usuario_id"])) {
        $errors[] = "O usuário é obrigatório!";
    } else if ($data["porcentagem_afiliado"] === '') {
        $errors[] = "Selecione a porcentagem!";
    } else {
        // Validar se é número inteiro
        if (!preg_match('/^\d+$/', $data["porcentagem_afiliado"])) {
            $errors[] = "Porcentagem inválida!";
        } else {
            $data["porcentagem_afiliado"] = (int)$data["porcentagem_afiliado"];

            try {
                // Verifica se o usuário existe
                $stmt = $pdo->prepare("SELECT id FROM bet_usuarios WHERE id = :id");
                $stmt->execute([':id' => $data["usuario_id"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $errors[] = "Usuário não encontrado!";
                } else {
                    // Atualizar porcentagem
                    $update = $pdo->prepare("UPDATE bet_usuarios SET bet_afiliado_por = :porcentagem WHERE id = :id");
                    $update->execute([
                        ':porcentagem' => $data["porcentagem_afiliado"],
                        ':id' => $data["usuario_id"]
                    ]);
                }
            } catch (PDOException $e) {
                $errors[] = "Erro ao atualizar porcentagem: " . $e->getMessage();
            }
        }
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {
        $response = array(
            "status" => "alertasim",
            "message" => "<p class='alertasim'>Porcentagem atualizada com sucesso! <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenerar CSRF
        $_SESSION['csrf_token_porcentagemafiliado'] = bin2hex(random_bytes(32));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}