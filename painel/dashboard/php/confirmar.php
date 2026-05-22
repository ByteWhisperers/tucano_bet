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
require_once '../../../includes/config.php';

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
    $extrato_id = isset($_POST['extrato_id']) ? (int)$_POST['extrato_id'] : 0; 

    if (!valida_token_csrf('confirmar')) {
        $errors[] = "Falha na validação do token. Por favor, tente novamente.";
    } else {
        if ($extrato_id <= 0) {
            $errors[] = "ID do extrato inválido!";
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $stmt = $pdo->prepare("SELECT bet_valor, bet_usuario FROM bet_transacoes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $extrato_id]);
        $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($transacao) {
            $valorRetirada = $transacao['bet_valor'];
            $usuario_id = $transacao['bet_usuario'];

            $stmt = $pdo->prepare("SELECT bet_nome, bet_cpf FROM bet_usuarios WHERE id = :id");
            $stmt->execute([':id' => $usuario_id]);
            $usuarioDados = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuarioDados) {
                $cpfLimpo = preg_replace('/[^0-9]/', '', $usuarioDados['bet_cpf']);
                $host = $_SERVER['HTTP_HOST'];
                $callback_url = "https://{$host}/dashboard/php/callback_retirada.php";

                $dados = [
                    "valor" => number_format($valorRetirada, 2, '.', ''),
                    "nome" => $usuarioDados['bet_nome'],
                    "doc_tipo" => "cpf",
                    "doc_numero" => $cpfLimpo,
                    "callback_url" => $callback_url,
                    "external_reference" => ""
                ];

                $url = "https://api.gerapix.digital/v1/pix/payments/";
                $token = $TokenGeraPix;

                // Inicia cURL para enviar pagamento
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer $token",
                        "Content-Type: application/json"
                    ],
                    CURLOPT_POSTFIELDS => json_encode($dados)
                ]);

                $resposta = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $respostaJson = json_decode($resposta, true);

                if ($http_code === 201 && isset($respostaJson['id_transacao'])) {
                    $idTransacaoApi = $respostaJson['id_transacao'];

                    // Atualiza registro da transação com ID da API
                    $update = $pdo->prepare("UPDATE bet_transacoes SET bet_id_transacao = :id_transacao WHERE id = :id");
                    $update->execute([
                        ':id_transacao' => $idTransacaoApi,
                        ':id' => $extrato_id
                    ]);

                    // Gera novo token CSRF
                    $_SESSION['csrf_token_confirmar'] = bin2hex(random_bytes(32));

                    $response = [
                        "status" => "alertasim",
                        "message" => "<p class='alertasim'>Pagamento enviado com sucesso! <span><i class='fas fa-check'></i></span></p>"
                    ];
                } else {
                    $response = [
                        "status" => "alertanao",
                        "message" => "<p class='alertanao'>Erro ao processar pagamento via API. Código: {$http_code} <span><i class='fas fa-times'></i></span></p>"
                    ];
                }

            } else {
                $response = [
                    "status" => "alertanao",
                    "message" => "<p class='alertanao'>Usuário da transação não encontrado! <span><i class='fas fa-times'></i></span></p>"
                ];
            }

        } else {
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Transação não encontrada! <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>