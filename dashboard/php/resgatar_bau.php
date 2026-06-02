<?php
session_start();
require_once '../../includes/db.php';

// Verificar se é uma requisição AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    exit(json_encode(['status' => 'alertanao', 'message' => 'Acesso direto não permitido.']));
}

// Verificar sessão
if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode(['status' => 'alertanao', 'message' => 'Sessão expirada. Faça login novamente.']));
}

$usuario_id = $_SESSION['usuario_id'];
$bau_id = isset($_POST['bau_id']) ? (int)$_POST['bau_id'] : 0;
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

// Validar CSRF
if (empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token_resgatar_bau']) {
    exit(json_encode(['status' => 'alertanao', 'message' => 'Erro de validação CSRF.']));
}

if ($bau_id <= 0) {
    exit(json_encode(['status' => 'alertanao', 'message' => 'ID do baú inválido.']));
}

try {
    $pdo->beginTransaction();

    // Buscar o baú e validar posse e status
    $stmt = $pdo->prepare("SELECT * FROM bet_afiliados_baus WHERE id = ? AND usuario_id = ? FOR UPDATE");
    $stmt->execute([$bau_id, $usuario_id]);
    $bau = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bau) {
        throw new Exception('Baú não encontrado.');
    }

    if ($bau['status'] !== 'disponivel') {
        throw new Exception('Este baú não está disponível para resgate.');
    }

    $valor_recompensa = $bau['valor_recompensa'];
    $nivel = $bau['nivel'];

    // 1. Atualizar status do baú
    $stmtUpdateBau = $pdo->prepare("UPDATE bet_afiliados_baus SET status = 'resgatado', data_resgate = NOW() WHERE id = ?");
    $stmtUpdateBau->execute([$bau_id]);

    // 2. Creditar saldo ao usuário
    $stmtUpdateSaldo = $pdo->prepare("UPDATE bet_usuarios SET bet_saldo = bet_saldo + ? WHERE id = ?");
    $stmtUpdateSaldo->execute([$valor_recompensa, $usuario_id]);

    // 3. Registrar transação
    $stmtInsertTransacao = $pdo->prepare("INSERT INTO bet_transacoes (bet_usuario, bet_valor, bet_tipo, bet_status, bet_data, bet_origem) 
                                         VALUES (?, ?, 'Bônus Afiliação', 'Aprovado', NOW(), ?)");
    $stmtInsertTransacao->execute([$usuario_id, $valor_recompensa, "bau_nivel_{$nivel}"]);

    // Buscar novo saldo
    $stmtSaldo = $pdo->prepare("SELECT bet_saldo FROM bet_usuarios WHERE id = ?");
    $stmtSaldo->execute([$usuario_id]);
    $novo_saldo = $stmtSaldo->fetchColumn();

    $pdo->commit();

    echo json_encode([
        'status' => 'alertasim',
        'novo_saldo' => number_format($novo_saldo, 2, ',', '.'),
        'message' => 'Parabéns! Você resgatou R$ ' . number_format($valor_recompensa, 2, ',', '.') . ' do baú nível ' . $nivel . '.'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'alertanao', 'message' => $e->getMessage()]);
}
?>
