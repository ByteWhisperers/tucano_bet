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
        "retirada" => trim($_POST["retirada"])
    );

    // Validações
    if (!valida_token_csrf('retirada')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["retirada"])) {
        $errors[] = "O campo retirada é obrigatório!";
    } else {
        $cleanValue = str_replace(array('R$', ' ', ' '), array('', '', ''), $data["retirada"]);
        $cleanValue = str_replace('.', '', $cleanValue);
        $cleanValue = str_replace(',', '.', $cleanValue);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
            $errors[] = "Valor não aceito!";
        } else {
            $data["retirada"] = (float)$cleanValue;

            if ($data["retirada"] < $ValorRetirada) {
                $errors[] = "A retirada mínima é de R$ " . number_format($ValorRetirada, 2, ',', '.') . " reais!";
            } elseif ($data["retirada"] > 10000) {
                $errors[] = "A retirada máxima é de R$ 10.000,00 reais!";
            } else {
// Busca o saldo do usuário e verifica se é influenciador
try {
    $stmt = $pdo->prepare("SELECT bet_saldo, bet_influenciador FROM bet_usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // Verifica se o usuário é influenciador
        if ($user['bet_influenciador']) {
            $errors[] = "Conta influenciador. Saque não é permitido!";
        } else if ($data["retirada"] > $user['bet_saldo']) {
            $errors[] = "Saldo insuficiente para a retirada!";
        }

    } else {
        $errors[] = "Usuário não encontrado!";
    }
} catch (PDOException $e) {
    $errors[] = "Erro ao consultar o saldo!";
}
            }
        }
    }

    if (empty($errors)) {

        // Verifica o tipo de pagamento
        if ($TipoPagamento === 0) {
            // Pagamento manual 
            $insert = $pdo->prepare("INSERT INTO bet_transacoes (bet_usuario, bet_valor, bet_tipo, bet_status, bet_data) 
                VALUES (:usuario, :valor, :tipo, 'Pendente', NOW())");

            $insert->execute([
                ':usuario' => $_SESSION['usuario_id'],
                ':valor'   => $data["retirada"],
                ':tipo'    => 'Retirada'
            ]);

            // Atualiza saldo
            $updateSaldo = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = bet_saldo - :valor WHERE id = :usuario");
            $updateSaldo->execute([
                ':valor'   => $data["retirada"],
                ':usuario' => $_SESSION['usuario_id']
            ]);

            $successMessage = "Retirada enviada com sucesso!";

        } elseif ($TipoPagamento === 1) {

        try {

            $pdo->beginTransaction();

                // 🔒 Bloqueia o usuário para evitar concorrência
                $stmtUser = $pdo->prepare("SELECT bet_nome, bet_cpf, bet_saldo FROM bet_usuarios WHERE id = :id FOR UPDATE");
                $stmtUser->execute([':id' => $_SESSION['usuario_id']]);
                $usuarioDados = $stmtUser->fetch(PDO::FETCH_ASSOC);

                  if (!$usuarioDados) {
 
                      $pdo->rollBack();
                      $errors[] = "Usuário não encontrado!";

                  }else {

                    // 🔒 Verifica retirada pendente
                    $verifica = $pdo->prepare(" SELECT COUNT(*) FROM bet_transacoes WHERE bet_usuario = :usuario AND bet_tipo = 'Retirada' AND bet_status = 'Pendente' FOR UPDATE");
                    $verifica->execute([':usuario' => $_SESSION['usuario_id']]);

                    if ($verifica->fetchColumn() > 0) {

                        $pdo->rollBack();
                        $errors[] = "Você já possui uma retirada pendente. Aguarde a conclusão.";

                  }else {

                        $valor = floatval($data["retirada"]);

                        if ($usuarioDados['bet_saldo'] < $valor) {

                            $pdo->rollBack();
                            $errors[] = "Saldo insuficiente.";

                        }else {

                           // 🔒 Desconta saldo
                           $updateSaldo = $pdo->prepare(" UPDATE bet_usuarios SET bet_saldo = bet_saldo - :valor WHERE id = :usuario ");
                           $updateSaldo->execute([
                           ':valor'   => $data["retirada"],
                           ':usuario' => $_SESSION['usuario_id']
                            ]);

                           // 🔒 Cria transação interna primeiro
                           $insert = $pdo->prepare("
                           INSERT INTO bet_transacoes (bet_usuario, bet_id_transacao, bet_valor, bet_tipo, bet_status, bet_data) VALUES (:usuario, :id_api, :valor, 'Retirada', 'Pendente', NOW()) ");
                           $insert->execute([
                           ':usuario' => $_SESSION['usuario_id'],
                           ':id_api'  => 'PROCESSANDO_' . time(),
                           ':valor'   => $data["retirada"]
                            ]);

                            $transacaoIdInterna = $pdo->lastInsertId();
                            $pdo->commit(); // 🔓 Libera banco antes da API

                            // 🔽 CHAMA A GERAPIX

                            $cpfLimpo = preg_replace('/[^0-9]/', '', $usuarioDados['bet_cpf']);
                            $host = $_SERVER['HTTP_HOST'];
                            $callback_url = "https://{$host}/dashboard/php/callback_retirada.php";

                            $dados = [
                            "valor" => number_format($data["retirada"], 2, '.', ''),
                            "nome" => $usuarioDados['bet_nome'],
                            "doc_tipo" => "cpf",
                            "doc_numero" => $cpfLimpo,
                            "callback_url" => $callback_url,
                            "external_reference" => "ret_" . $transacaoIdInterna
                            ];


                            $ch = curl_init("https://api.gerapix.digital/v1/pix/payments/");
                            curl_setopt_array($ch, [
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POST => true,
                                CURLOPT_HTTPHEADER => [
                                    "Authorization: Bearer $TokenGeraPix",
                                    "Content-Type: application/json"
                                ],
                                CURLOPT_POSTFIELDS => json_encode($dados),
                                CURLOPT_TIMEOUT => 30
                            ]);

                            $resposta = curl_exec($ch);
                            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            $respostaJson = json_decode($resposta, true);

                            if ($http_code === 201 && isset($respostaJson['id_transacao'])) {

                                $updateTransacao = $pdo->prepare("UPDATE bet_transacoes SET bet_id_transacao = :id_api, bet_status = 'Pendente' WHERE id = :id");
                                $updateTransacao->execute([
                                ':id_api' => $respostaJson['id_transacao'],
                                ':id'     => $transacaoIdInterna
                                ]);

                                $successMessage = "Retirada enviada com sucesso!";

                            }else {

                                // 🔁 Estorna saldo se API falhar
                                $pdo->beginTransaction();

                                $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = bet_saldo + :valor WHERE id = :usuario")          
                                ->execute([
                                ':valor'   => $data["retirada"],
                                ':usuario' => $_SESSION['usuario_id']
                                ]);

                                $pdo->prepare("UPDATE bet_transacoes SET bet_status = 'Cancelado' WHERE id = :id")
                                ->execute([
                                ':id' => $transacaoIdInterna
                                ]);

                                $pdo->commit();

                                if (isset($respostaJson['error_code']) && $respostaJson['error_code'] === 'ERR002') {

                                $errors[] = "Retirada em manutenção. Tente novamente mais tarde!";

                                }else {

                                $errors[] = "Retirada em manutenção. Tente novamente mais tarde!";

                                }
                            }
                        }
                   }
                }
            } catch (Exception $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

            $errors[] = "Erro interno ao processar retirada.";
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
            "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenera o token CSRF
        $_SESSION['csrf_token_retirada'] = bin2hex(random_bytes(32));
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}