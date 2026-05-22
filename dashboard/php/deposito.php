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
        "deposito" => trim($_POST["deposito"])
    );

     // Verifica se o valor do depósito está vazio
    if (!valida_token_csrf('deposito')) {
    $errors[] = "Falha. Por favor, tente novamente.";
    } else if (empty($data["deposito"])) {
        $errors[] = "O campo depósito é obrigatório!";
    } else {
        // Limpa o valor, removendo "R$", espaços e transformando a vírgula em ponto
        $cleanValue = str_replace(array('R$', ' ', ' '), array('', '', ''), $data["deposito"]);
        $cleanValue = str_replace('.', '', $cleanValue); // Remove o ponto
        $cleanValue = str_replace(',', '.', $cleanValue); // Troca a vírgula por ponto para o formato float

        // Verifica se o valor está correto
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cleanValue)) {
            $errors[] = "Valor não aceito!";
        } else {
            // Converte para float
            $data["deposito"] = (float)$cleanValue;

            // Verifica os limites de depósito
            if ($data["deposito"] < $ValorDeposito) {
                $errors[] = "O depósito mínimo é de R$ " . number_format($ValorDeposito, 2, ',', '.') . " reais!";
            }
            if ($data["deposito"] > 10000) {
                $errors[] = "O depósito máximo é de R$ 10.000,00 reais!";
            }
        }
    }

    if (!empty($errors)) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {
     
    try {
    $pdo->beginTransaction();

    $usuario_id = $_SESSION['usuario_id'];
    $valorOriginal = $data["deposito"];
    $callback_url = 'https://' . $_SERVER['HTTP_HOST'] . '/dashboard/php/callback_deposito.php';

    // Buscar dados do cliente
    $stmt = $pdo->prepare("SELECT bet_nome, bet_email, bet_cpf, bet_origem FROM bet_usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        throw new Exception("Cliente não encontrado.");
    }

    // === Geração do QR Code via GeraPix ===
    $ch = curl_init('https://api.gerapix.digital/v1/pix/qrcodes/');

    $payload = json_encode([
        'valor' => number_format($valorOriginal, 2, '.', ''),
        'nome' => $cliente['bet_nome'],
        'email' => $cliente['bet_email'],
        'doc_tipo' => 'cpf',
        'doc_numero' => $cliente['bet_cpf'],
        'callback_url' => $callback_url,
        'split' => [
            [
                'login_split' => $LoginSplit,
                'porcentagem_split' => $PorcentagemSplit
            ]
        ]
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $TokenGeraPix,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $resultadoPix = json_decode($response, true);

    // Verifica resposta da API
    $id_transacao = $resultadoPix['id_transacao'] ?? null;
    $qr_code_base64 = $resultadoPix['qr_code_base64'] ?? null;
    $qr_code = $resultadoPix['qr_code'] ?? null;

    if (!$id_transacao || !$qr_code) {
        throw new Exception("Erro na geração do QR Code.");
    }

$origem     = $cliente['bet_origem'];
$ipUsuario  = $_SERVER['REMOTE_ADDR'];
$fbc        = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';
$fbp        = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';

// Inserir transação
$insert = $pdo->prepare("INSERT INTO bet_transacoes 
    (bet_usuario, bet_id_transacao, bet_valor, bet_tipo, bet_status, bet_data, bet_origem, bet_ip, bet_fbc, bet_fbp) 
    VALUES 
    (:usuario, :id_transacao, :valor, :tipo, 'Pendente', NOW(), :origem, :ip, :fbc, :fbp)");

$insert->execute([
    ':usuario'      => $usuario_id,
    ':id_transacao' => $id_transacao,
    ':valor'        => $valorOriginal, // valor já formatado
    ':tipo'         => 'Deposito',     // ou 'Retirada', dependendo do caso
    ':origem'       => $origem,
    ':ip'          => $ipUsuario,
    ':fbc'         => $fbc,
    ':fbp'         => $fbp
]);


    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack(); // Reverte transação em caso de erro
}

        // Define o HTML do conteúdo do PIX mantendo a estrutura do modal
                $html = '
                <div class="modal-content">
                    <span class="close-modal"><i class="fas fa-times"></i></span>
                    <h2>Depósito</h2>
                    <div class="pix-container">
                        <p>Se preferir, você pode depositar seu saldo lendo o código QR no aplicativo do seu banco.</p>
                        <img src="' . (!empty($qr_code_base64) ? $qr_code_base64 : '../../imagens/gerapix.png') . '" alt="QR Code PIX" />
                        <p>Se preferir, você pode depositar seu saldo copiando e colando o código abaixo:</p>
                        
                        <div class="form-row">
                            <div class="input-icon">
                            <i class="fas fa-clipboard-check"></i>
                            <input type="text" id="pixLink" value="' . $qr_code . '" placeholder="Link PIX" readonly>
                        </div>
                        </div>
                        <input type="hidden" name="txidmodal" value="' . $id_transacao . '">
                        <button class="submit-button BotaoCopiaPix" onclick="copiarCodigo()">Copiar Código</button>
                        <p style="text-align: center;">Valor do PIX: R$ <strong style="color:'.$corPrincipal.'">' . number_format($data["deposito"], 2, ',', '.') . '</strong> reais.</p>
                    </div>
                </div>';

                // Adicione o script JavaScript aqui
$html .= "
<script type=\"text/javascript\">
    function checkStatus(interval) {
        const txid = document.querySelector('input[name=\"txidmodal\"]').value;

        $.ajax({
            url: `php/check_pagamento.php?txid=\${encodeURIComponent(txid)}&t=\${new Date().getTime()}`, // Adiciona timestamp
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const status = response.status;

                if (status == 'Pendente') {
                    // Mensagem de console
                } else if (status == 'Aprovado') {
                    clearInterval(interval);
                    // Redireciona para o dashboard
                    window.location.href = '/dashboard/';
                } else {
                    // Mensagem para outros status
                }
            },
            error: function() {
                // Mensagem de erro
            }
        });
    }

    const interval = setInterval(function() {
        checkStatus(interval);
    }, 3000);
</script>
";

                // Define a resposta JSON com status "alertasim" e o HTML do modal
                $response = array(
                    "status" => "alertasim",
                    "html" => $html
                );

        // Regenera o token CSRF após um envio bem-sucedido
        $_SESSION['csrf_token_deposito'] = bin2hex(random_bytes(32));
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}