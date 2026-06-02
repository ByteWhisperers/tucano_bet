<?php
/**
 * Sistema de Afiliação Gamificado - Baús
 */

function inicializarBausUsuario($pdo, $usuarioId) {
    $niveis = [
        ['nivel' => 1, 'pessoas' => 1, 'valor' => 10.00],
        ['nivel' => 2, 'pessoas' => 3, 'valor' => 30.00],
        ['nivel' => 3, 'pessoas' => 5, 'valor' => 75.00],
        ['nivel' => 4, 'pessoas' => 10, 'valor' => 200.00],
        ['nivel' => 5, 'pessoas' => 20, 'valor' => 500.00],
    ];

    foreach ($niveis as $n) {
        $sql = "INSERT IGNORE INTO bet_afiliados_baus (usuario_id, nivel, pessoas_necessarias, valor_recompensa, status) 
                VALUES (?, ?, ?, ?, 'bloqueado')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId, $n['nivel'], $n['pessoas'], $n['valor']]);
    }
}

function contarIndicadosValidos($pdo, $afiliadoId) {
    // Conta indicados que fizeram pelo menos um depósito aprovado
    $sql = "SELECT COUNT(DISTINCT u.id) as total 
            FROM bet_usuarios u 
            JOIN bet_transacoes t ON u.id = t.bet_usuario 
            WHERE u.bet_ref = ? AND t.bet_tipo = 'Deposito' AND t.bet_status = 'Aprovado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$afiliadoId]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$res['total'];
}

function atualizarProgressoBaus($pdo, $afiliadoId) {
    $totalIndicados = contarIndicadosValidos($pdo, $afiliadoId);
    
    $sql = "UPDATE bet_afiliados_baus 
            SET status = 'disponivel', data_desbloqueio = NOW() 
            WHERE usuario_id = ? AND status = 'bloqueado' AND pessoas_necessarias <= ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$afiliadoId, $totalIndicados]);
}

function processarProgressoBausAposDeposito($pdo, $indicadoId) {
    // Busca o afiliado (quem indicou)
    $sql = "SELECT bet_ref FROM bet_usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$indicadoId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['bet_ref'] > 0) {
        $afiliadoId = (int)$user['bet_ref'];
        
        // Verifica se é o primeiro depósito aprovado deste indicado
        $sql = "SELECT COUNT(*) as total FROM bet_transacoes 
                WHERE bet_usuario = ? AND bet_tipo = 'Deposito' AND bet_status = 'Aprovado'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indicadoId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ((int)$res['total'] === 1) {
            // Inicializa baús para o afiliado caso não existam
            inicializarBausUsuario($pdo, $afiliadoId);
            // Atualiza progresso
            atualizarProgressoBaus($pdo, $afiliadoId);
        }
    }
}
?>
