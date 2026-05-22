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

    // Sanitiza e valida os dados de entrada
    $data = [
        "deposito" => trim($_POST["deposito"] ?? ''),
        "retirada" => trim($_POST["retirada"] ?? ''),
        "pagamento_auto" => isset($_POST["pagamento_auto"]) ? (int)$_POST["pagamento_auto"] : 0
    ];

    if (!valida_token_csrf('valores')) { 
    $errors[] = "Falha. Por favor, tente novamente.";
} else {
    // Validação depósito
    if (empty($data["deposito"])) {
        $errors[] = "O campo valor de Depósito é obrigatório!";
    } else {
        $cleanValue = str_replace(array('R$', ' ', ' '), array('', '', ''), $data["deposito"]);
        $cleanValue = str_replace('.', '', $cleanValue);
        $cleanValue = str_replace(',', '.', $cleanValue);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
            $errors[] = "Valor mínimo de Depósito não aceito!";
        } else {
            $data["deposito"] = (float)$cleanValue;

            if ($data["deposito"] < 10) {
                $errors[] = "Valor mínimo é de R$ 10,00 reais!";
            }
        }
    }

    // Só valida retirada se não houver erro no depósito
    if (empty($errors)) {
        // Validação retirada
        if (empty($data["retirada"])) {
            $errors[] = "O campo valor de Retirada é obrigatório!";
        } else {
            $cleanValue = str_replace(array('R$', ' ', ' '), array('', '', ''), $data["retirada"]);
            $cleanValue = str_replace('.', '', $cleanValue);
            $cleanValue = str_replace(',', '.', $cleanValue);

            if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
                $errors[] = "Valor de Retirada não aceito!";
            } else {
                $data["retirada"] = (float)$cleanValue;

                if ($data["retirada"] < 10) {
                    $errors[] = "Valor mínimo é de R$ 10,00 reais!";
                }
            }
        }
    }
}


    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_valor_deposito = :deposito, bet_valor_retirada = :retirada, bet_pag_tipo = :pagamento_auto  WHERE id = 1");
            $stmt->bindParam(':deposito', $data['deposito']);
            $stmt->bindParam(':retirada', $data['retirada']);
            $stmt->bindParam(':pagamento_auto', $data['pagamento_auto'], PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['csrf_token_valores'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Valores atualizados com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];
        } catch (PDOException $e) {
            $response = [
                "status" => "alertnao",
                "message" => "<p class='alertnao'>Erro ao atualizar: " . $e->getMessage() . " <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>