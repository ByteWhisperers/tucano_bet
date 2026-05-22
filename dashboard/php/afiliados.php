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

// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    $data = array(
        "id_transacoes" => filter_input(INPUT_POST, "id_transacoes", FILTER_SANITIZE_NUMBER_INT)
    );

    // Validações
    if (!valida_token_csrf('afiliados')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($_SESSION['usuario_id']) || !is_numeric($_SESSION['usuario_id']) || $_SESSION['usuario_id'] <= 0) {
        $errors[] = "Sessão expirada. Faça login novamente.";
    } else if (empty($data['id_transacoes']) || !is_numeric($data['id_transacoes']) || $data['id_transacoes'] <= 0) {
        $errors[] = "Transação inválida.";
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
    $usuario_id = (int) $_SESSION['usuario_id'];
    $id_transacao = (int) $data['id_transacoes'];

    // Começa a transação
    $pdo->beginTransaction();

    // Busca porcentagem e saldo do usuário
    $stmtPorcentagem = $pdo->prepare("SELECT bet_afiliado_por, bet_saldo FROM bet_usuarios WHERE id = :usuario_id LIMIT 1");
    $stmtPorcentagem->execute(['usuario_id' => $usuario_id]);
    $row = $stmtPorcentagem->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->rollBack();
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>Usuário não encontrado. <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $porcentagem = (float)$row['bet_afiliado_por'];
        $saldo_atual = (float)$row['bet_saldo'];

        // Busca valor da transação
        $stmtValor = $pdo->prepare("SELECT bet_valor FROM bet_transacoes WHERE id = :id LIMIT 1");
        $stmtValor->execute(['id' => $id_transacao]);
        $valor_transacao = $stmtValor->fetchColumn();

        if ($valor_transacao === false) {
            $pdo->rollBack();
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Transação não encontrada. <span><i class='fas fa-times'></i></span></p>"
            ];
        } else {
            $valor_bonus = $valor_transacao * ($porcentagem / 100);

            // Atualiza a transação original
            $updateStmt = $pdo->prepare("UPDATE bet_transacoes SET bet_afiliado_bonus = :valor_bonus, bet_afiliado_status = 1 WHERE id = :id_transacao");
            $updateResult = $updateStmt->execute([
                'valor_bonus' => $valor_bonus,
                'id_transacao' => $id_transacao
            ]);

            if (!$updateResult) {
                $pdo->rollBack();
                $response = [
                    "status" => "alertanao",
                    "message" => "<p class='alertanao'>Erro ao atualizar o bônus. <span><i class='fas fa-times'></i></span></p>"
                ];
            } else {
                $data_atual = date('Y-m-d H:i:s');

                // Insere nova transação de bônus
                $insertStmt = $pdo->prepare("
                    INSERT INTO bet_transacoes (bet_usuario, bet_valor, bet_tipo, bet_status, bet_data)
                    VALUES (:usuario_id, :valor_bonus, 'Bônus', 'Aprovado', :data_atual)
                ");
                $insertResult = $insertStmt->execute([
                    'usuario_id' => $usuario_id,
                    'valor_bonus' => $valor_bonus,
                    'data_atual' => $data_atual
                ]);

                if (!$insertResult) {
                    $pdo->rollBack();
                    $response = [
                        "status" => "alertanao",
                        "message" => "<p class='alertanao'>Erro ao registrar o bônus. <span><i class='fas fa-times'></i></span></p>"
                    ];
                } else {
                    // Atualiza o saldo do usuário somando o bônus
                    $novo_saldo = $saldo_atual + $valor_bonus;
                    $updateSaldoStmt = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = :novo_saldo WHERE id = :usuario_id");
                    $updateSaldoResult = $updateSaldoStmt->execute([
                        'novo_saldo' => $novo_saldo,
                        'usuario_id' => $usuario_id
                    ]);

                    if (!$updateSaldoResult) {
                        $pdo->rollBack();
                        $response = [
                            "status" => "alertanao",
                            "message" => "<p class='alertanao'>Erro ao atualizar saldo. <span><i class='fas fa-times'></i></span></p>"
                        ];
                    } else {
                        $pdo->commit();
                        $response = [
                            "status" => "alertasim",
                            "message" => "<p class='alertasim'>Bônus resgatado e saldo atualizado! <span><i class='fas fa-check'></i></span></p>"
                        ];

                        // Regenera token CSRF
                        $_SESSION['csrf_token_afiliados'] = bin2hex(random_bytes(32));
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response = [
        "status" => "alertanao",
        "message" => "<p class='alertanao'>Erro inesperado: " . htmlspecialchars($e->getMessage()) . " <span><i class='fas fa-times'></i></span></p>"
    ];
}

    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}