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
    $errors = [];

    $data = [
        "descricao" => trim($_POST["bet_descricao"] ?? ''),
        "valor"     => trim($_POST["bet_valor"] ?? ''),
        "data"      => trim($_POST["bet_data"] ?? '')
    ];

    if (!valida_token_csrf('despesas')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["descricao"])) {
        $errors[] = "A descrição da despesa é obrigatória!";
    } else if (empty($data["valor"])) {
        $errors[] = "O valor da despesa é obrigatório!";
    } else {
        // Limpa e valida o valor
        $cleanValue = str_replace(['R$', ' ', ' '], '', $data["valor"]);
        $cleanValue = str_replace('.', '', $cleanValue);
        $cleanValue = str_replace(',', '.', $cleanValue);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
            $errors[] = "Valor da despesa inválido!";
        } else if (empty($data["data"])) {
            $errors[] = "A data da despesa é obrigatória!";
        } else if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data["data"])) {
            $errors[] = "Formato de data inválido!";
        } else {
            // Todas as validações passaram
            $data["valor"] = (float)$cleanValue;
            $partes = explode('/', $data["data"]);
            $data["data"] = $partes[2] . '-' . $partes[1] . '-' . $partes[0]; // yyyy-mm-dd

            try {
                $stmt = $pdo->prepare("INSERT INTO bet_despesas (bet_descricao, bet_valor, bet_data) VALUES (:descricao, :valor, :data)");
                $stmt->execute([
                    ':descricao' => $data["descricao"],
                    ':valor'     => $data["valor"],
                    ':data'      => $data["data"]
                ]);

                $response = [
                    "status"  => "alertasim",
                    "message" => "<p class='alertasim'>Despesa cadastrada com sucesso! <span><i class='fas fa-check'></i></span></p>"
                ];

                // Regenera o token CSRF
                $_SESSION['csrf_token_despesas'] = bin2hex(random_bytes(32));
            } catch (PDOException $e) {
                $errors[] = "Erro ao salvar despesa: " . htmlspecialchars($e->getMessage());
            }
        }
    }

    if (!empty($errors)) {
        $response = [
            "status"  => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}