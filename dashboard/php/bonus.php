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

// Função para validar CSRF
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    if (!isset($_SESSION['usuario_id'])) {
        $errors[] = "Você precisa estar logado.";
    } else if (!valida_token_csrf('bonus')) {
        $errors[] = "Falha. Por favor, tente novamente.";
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>{$errors[0]} <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $usuario_id = $_SESSION['usuario_id'];

        // Buscar saldo atual do usuário
        $stmt = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $saldo_atual = (float) $stmt->fetchColumn();

        // Buscar data do último bônus resgatado
        $stmt = $pdo->prepare("SELECT bet_data FROM bet_bonus WHERE bet_usuario = ? AND bet_bonus_status = 1 ORDER BY bet_data DESC LIMIT 1");
        $stmt->execute([$usuario_id]);
        $ultima_data_resgate = $stmt->fetchColumn();

        // Buscar o primeiro bônus pendente (status=0)
        $stmt = $pdo->prepare("SELECT id, bet_bonus_tipo, bet_bonus_valor, bet_data FROM bet_bonus WHERE bet_usuario = ? AND bet_bonus_status = 0 ORDER BY bet_data ASC LIMIT 1");
        $stmt->execute([$usuario_id]);
        $bonus = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bonus) {
            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Você não tem bônus pendentes para resgatar.<span><i class='fas fa-times'></i></span></p>"
            ];
        } else {
            $id_bonus = $bonus['id'];
            $tipo = $bonus['bet_bonus_tipo'];
            $valor = (float) $bonus['bet_bonus_valor'];
            $data_bonus = $bonus['bet_data'];

            if (in_array($tipo, ['Cadastro', 'Raspadinha'])) {
                $rotulo = $tipo === 'Cadastro' ? 'Bônus Cadastro' : 'Bônus Raspadinha';

                // Meta total do bônus: 2x valor + 10
                $meta_total = ($valor * 2) + 10;
                $meta_gasto = $meta_total - $valor;

                // Data para considerar depósitos: data do último resgate ou data original do bônus
                $data_deposito_inicio = $ultima_data_resgate ?: $data_bonus;

                // === Gasto ===
                $stmt = $pdo->prepare("SELECT bet_valor, bet_tipo FROM bet_transacoes WHERE bet_usuario = ? AND bet_status = 'Aprovado' AND bet_data >= ?");
                $stmt->execute([$usuario_id, $data_deposito_inicio]);
                $transacoes_gasto = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $total_depositos = 0;
                $total_saques = 0;
                foreach ($transacoes_gasto as $t) {
                    if ($t['bet_tipo'] === 'Deposito') {
                        $total_depositos += (float)$t['bet_valor'];
                    } elseif ($t['bet_tipo'] === 'Retirada') {
                        $total_saques += (float)$t['bet_valor'];
                    }
                }

                $gasto_bonus = $total_depositos - $total_saques - $saldo_atual;
                if ($gasto_bonus < 0) $gasto_bonus = 0;

                // === Depósito ===
                $stmt = $pdo->prepare("SELECT SUM(bet_valor) FROM bet_transacoes WHERE bet_usuario = ? AND bet_tipo = 'Deposito' AND bet_status = 'Aprovado' AND bet_data >= ?");
                $stmt->execute([$usuario_id, $data_deposito_inicio]);
                $deposito_utilizado = (float) $stmt->fetchColumn();

                // === Validação final ===
                if ($deposito_utilizado >= $meta_total && $gasto_bonus >= $meta_gasto) {
                    // Libera o bônus e atualiza a data de resgate para o momento atual
                    $dataAtual = date('Y-m-d H:i:s');
                    $stmt = $pdo->prepare("UPDATE bet_bonus SET bet_bonus_status = 1, bet_data = ? WHERE id = ?");
                    $stmt->execute([$dataAtual, $id_bonus]);

                    $stmt = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = bet_saldo + ? WHERE id = ?");
                    $stmt->execute([$valor, $usuario_id]);

                    $_SESSION['csrf_token_bonus'] = bin2hex(random_bytes(32));

                    $response = [
                        "status" => "alertasim",
                        "message" => "<p class='alertasim'>Você resgatou 1 bônus com sucesso!<span><i class='fas fa-check'></i></span></p>"
                    ];
                } else {
                    $faltando_deposito = max(0, $meta_total - $deposito_utilizado);
                    $faltando_gasto = max(0, $meta_gasto - $gasto_bonus);

                    $mensagem = "
                    <div class='bonus-wrapper'>
                        <ul class='bonus-status'>
                            <li>{$rotulo} de R$ <strong class='valor'>" . number_format($valor, 2, ',', '.') . "</strong>
                            <ul>";

                    if ($faltando_deposito > 0) {
                        $mensagem .= "<li>- Falta <span class='highlight'>depositar</span> R$ <strong class='valor'>" . number_format($faltando_deposito, 2, ',', '.') . "</strong></li>";
                    }
                    if ($faltando_gasto > 0) {
                        $mensagem .= "<li>- Falta <span class='highlight'>jogar</span> R$ <strong class='valor'>" . number_format($faltando_gasto, 2, ',', '.') . "</strong></li>";
                    }

                    $mensagem .= "</ul></li></ul></div>";

                    $response = [
                        "status" => "alertanao",
                        "message" => "<p class='alertanao'>Você ainda não cumpriu os requisitos para resgatar:<br>{$mensagem}</p>"
                    ];
                }
            } else {
                $response = [
                    "status" => "alertanao",
                    "message" => "<p class='alertanao'>Tipo de bônus inválido.<span><i class='fas fa-times'></i></span></p>"
                ];
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
