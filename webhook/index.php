<?php
include_once '../includes/db.php';    
include_once '../includes/config.php';

$response = ['msg' => 'INVALID_REQUEST_TYPE', 'balance' => 0];

try {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);

    if (!isset($data['agent_secret']) || $data['agent_secret'] !== $TokenPlayFiverSecreto) {
        $response = ['msg' => 'INVALID_AGENT_SECRET', 'balance' => 0];
        echo json_encode($response);
        exit;
    }

    if (isset($data['type'])) {

        if ($data['type'] === 'BALANCE') {
            // Consulta saldo do usuário
            $userCode = $data['user_code'];
            $stmt = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = :user_code LIMIT 1");
            $stmt->bindParam(':user_code', $userCode, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $balance = (float)$usuario['bet_saldo'];
                $response = ['msg' => '', 'balance' => $balance];
            } else {
                $response = ['msg' => 'INVALID_USER', 'balance' => 0];
            }

        } elseif ($data['type'] === 'WinBet') {
            // Processa WinBet (slot ou live)
            $userCode = $data['user_code'];
            $gameType = $data['game_type'] ?? 'slot';

            // Dados do jogo
            $gameData = $data[$gameType] ?? [];

            $bet = isset($gameData['bet']) ? (float)$gameData['bet'] : 0;
            $win = isset($gameData['win']) ? (float)$gameData['win'] : 0;
            $txnType = $gameData['txn_type'] ?? null;

            // Busca saldo atual
            $stmt = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = :user_code LIMIT 1");
            $stmt->bindParam(':user_code', $userCode, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                $response = ['msg' => 'INVALID_USER', 'balance' => 0];
            } else {
                $saldoAtual = (float)$usuario['bet_saldo'];
                $novoSaldo = $saldoAtual;

                if ($txnType === 'debit_credit') {
                    // Debita aposta, credita ganho
                    $novoSaldo = $saldoAtual - $bet + $win;
                    if ($novoSaldo < 0) {
                        $response = ['msg' => 'INSUFFICIENT_FUNDS', 'balance' => $saldoAtual];
                    } else {
                        $update = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = :novo_saldo WHERE id = :user_code");
                        $update->bindValue(':novo_saldo', $novoSaldo, PDO::PARAM_STR);
                        $update->bindValue(':user_code', $userCode, PDO::PARAM_INT);
                        $update->execute();

                        $response = ['msg' => 'SUCCESS', 'balance' => $novoSaldo];
                    }
                } elseif ($txnType === 'bonus') {
                    // Só adiciona bônus
                    $novoSaldo = $saldoAtual + $win;
                    $update = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = :novo_saldo WHERE id = :user_code");
                    $update->bindValue(':novo_saldo', $novoSaldo, PDO::PARAM_STR);
                    $update->bindValue(':user_code', $userCode, PDO::PARAM_INT);
                    $update->execute();

                    $response = ['msg' => 'BONUS_ADDED', 'balance' => $novoSaldo];
                } else {
                    $response = ['msg' => 'INVALID_TRANSACTION_TYPE', 'balance' => $saldoAtual];
                }
            }
        }
    }
} catch (Exception $e) {
    $response = ['msg' => 'ERROR', 'balance' => 0];
}

header('Content-Type: application/json');
echo json_encode($response);
exit;