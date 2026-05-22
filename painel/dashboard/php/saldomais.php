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
        "valor" => trim($_POST["valor"] ?? '')
    );

    if (!valida_token_csrf('saldomais')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["usuario_id"])) {
        $errors[] = "O usuário é obrigatório!";
    } else if (empty($data["valor"])) {
        $errors[] = "O valor é obrigatório!";
    } else {
        // Todas as validações anteriores passaram, segue validação do formato e processamento
        $cleanValue = str_replace(array('R$', ' ', ' '), array('', '', ''), $data["valor"]);
        $cleanValue = str_replace('.', '', $cleanValue);
        $cleanValue = str_replace(',', '.', $cleanValue);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
            $errors[] = "Valor informado é inválido!";
        } else {
            $data["valor"] = (float)$cleanValue;

            try {
                // Buscar saldo atual
                $stmt = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = :id");
                $stmt->execute([':id' => $data["usuario_id"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $errors[] = "Usuário não encontrado!";
                } else {
                    $novoSaldo = $user['bet_saldo'] + $data["valor"];

                    // Atualizar saldo
                    $update = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = :bet_saldo WHERE id = :id");
                    $update->execute([
                        ':bet_saldo' => $novoSaldo,
                        ':id' => $data["usuario_id"]
                    ]);
                }
            } catch (PDOException $e) {
                $errors[] = "Erro ao atualizar saldo: " . $e->getMessage();
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
            "message" => "<p class='alertasim'>Saldo atualizado com sucesso! <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenerar CSRF
        $_SESSION['csrf_token_saldomais'] = bin2hex(random_bytes(32));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}