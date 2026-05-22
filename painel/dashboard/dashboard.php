<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}

// Consulta bet_usuarios
$stmt = $pdo->prepare("
    SELECT
        COUNT(id) AS total_usuarios,
        SUM(CASE WHEN DATE(bet_data) = CURDATE() THEN 1 ELSE 0 END) AS novos_hoje,
        SUM(CASE WHEN YEARWEEK(bet_data, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) AS novos_semana,
        SUM(CASE WHEN YEAR(bet_data) = YEAR(CURDATE()) AND MONTH(bet_data) = MONTH(CURDATE()) THEN 1 ELSE 0 END) AS novos_mes,
        SUM(bet_saldo) AS saldo_total
    FROM bet_usuarios
");
$stmt->execute();
$resultUsuarios = $stmt->fetch(PDO::FETCH_ASSOC);

$totalUsuarios = $resultUsuarios['total_usuarios'] ?? 0;
$novosHoje     = $resultUsuarios['novos_hoje'] ?? 0;
$novosSemana   = $resultUsuarios['novos_semana'] ?? 0;
$novosMes      = $resultUsuarios['novos_mes'] ?? 0;
$saldoPlayers  = $resultUsuarios['saldo_total'] ?? 0;

// Consulta bet_transacoes
$stmt = $pdo->prepare("
    SELECT
        SUM(CASE WHEN bet_tipo = 'Deposito' AND bet_status = 'Aprovado' THEN bet_valor ELSE 0 END) AS depositos_totais,
        SUM(CASE WHEN bet_tipo = 'Deposito' AND bet_status = 'Pendente' THEN bet_valor ELSE 0 END) AS depositos_pendentes,
        SUM(CASE WHEN bet_tipo = 'Retirada' AND bet_status = 'Aprovado' THEN bet_valor ELSE 0 END) AS saques_pagos,
        SUM(CASE WHEN bet_tipo = 'Retirada' AND bet_status = 'Pendente' THEN bet_valor ELSE 0 END) AS saques_pendentes,
        SUM(CASE WHEN bet_tipo = 'Retirada' THEN bet_valor ELSE 0 END) AS saques_totais,
        SUM(CASE WHEN bet_tipo = 'Deposito' AND bet_status = 'Aprovado' AND DATE(bet_data) = CURDATE() THEN bet_valor ELSE 0 END) AS depositos_hoje,
        SUM(CASE WHEN bet_tipo = 'Retirada' AND DATE(bet_data) = CURDATE() THEN bet_valor ELSE 0 END) AS saques_hoje
    FROM bet_transacoes
");
$stmt->execute();
$resultTransacoes = $stmt->fetch(PDO::FETCH_ASSOC);

$depositosTotais   = $resultTransacoes['depositos_totais'] ?? 0;
$depositosPendentes = $resultTransacoes['depositos_pendentes'] ?? 0;
$saquesPagos       = $resultTransacoes['saques_pagos'] ?? 0;
$saquesPendentes   = $resultTransacoes['saques_pendentes'] ?? 0;
$saquesTotais      = $resultTransacoes['saques_totais'] ?? 0;
$depositosHoje     = $resultTransacoes['depositos_hoje'] ?? 0;
$saquesHoje        = $resultTransacoes['saques_hoje'] ?? 0;

// Consulta bet_despesas
$stmt = $pdo->prepare("SELECT SUM(bet_valor) AS total_despesas FROM bet_despesas");
$stmt->execute();
$resultDespesas = $stmt->fetch(PDO::FETCH_ASSOC);
$despesas = $resultDespesas['total_despesas'] ?? 0;

// Cálculos finais
$lucroBruto  = $depositosTotais - $saquesPagos;
$lucroLiquido = $lucroBruto - $saldoPlayers - $despesas;
?>

<style>
    .container-conteudo {
        max-width: 1000px;
        margin: 50px auto;
        box-sizing: border-box;
        width: 100%;
    }
    .negativo {
        color: red;
    }
</style>

<div class="container-conteudo">
    <h2 class="titulo-funcoes">Estatísticas</h2>
    <div class="grid-boxes">

        <!-- Total de Usuários -->
        <div class="box-funcao">
            <i class="fas fa-users"></i>
            <span>Total de Usuários</span>
            <span class="valor"><?= $totalUsuarios ?></span>
        </div>

        <!-- Novos Hoje -->
        <div class="box-funcao">
            <i class="fas fa-user-plus"></i>
            <span>Novos Hoje</span>
            <span class="valor"><?= $novosHoje ?></span>
        </div>

        <!-- Novos Essa Semana -->
        <div class="box-funcao">
            <i class="fas fa-calendar-week"></i>
            <span>Novos Essa Semana</span>
            <span class="valor"><?= $novosSemana ?></span>
        </div>

        <!-- Novos Esse Mês -->
        <div class="box-funcao">
            <i class="fas fa-calendar-alt"></i>
            <span>Novos Esse Mês</span>
            <span class="valor"><?= $novosMes ?></span>
        </div>

        <!-- Depósitos Totais -->
        <div class="box-funcao">
            <i class="fas fa-wallet"></i>
            <span>Depósitos Totais</span>
            <span class="valor">R$ <?= number_format($depositosTotais, 2, ',', '.') ?></span>
        </div>

        <!-- Depósitos Hoje -->
        <div class="box-funcao">
            <i class="fas fa-coins"></i>
            <span>Depósitos Hoje</span>
            <span class="valor">R$ <?= number_format($depositosHoje, 2, ',', '.') ?></span>
        </div>

        <!-- Depósitos Pendentes -->
        <div class="box-funcao">
            <i class="fas fa-hourglass-half"></i>
            <span>Depósitos Pendentes</span>
            <span class="valor">R$ <?= number_format($depositosPendentes, 2, ',', '.') ?></span>
        </div>

        <!-- Total Saques -->
        <div class="box-funcao">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Total Saques</span>
            <span class="valor">R$ <?= number_format($saquesTotais, 2, ',', '.') ?></span>
        </div>

        <!-- Saques Pagos -->
        <div class="box-funcao">
            <i class="fas fa-check-circle"></i>
            <span>Saques Pagos</span>
            <span class="valor">R$ <?= number_format($saquesPagos, 2, ',', '.') ?></span>
        </div>

        <!-- Saques Pendentes -->
        <div class="box-funcao">
            <i class="fas fa-clock"></i>
            <span>Saques Pendentes</span>
            <span class="valor">R$ <?= number_format($saquesPendentes, 2, ',', '.') ?></span>
        </div>

        <!-- Saques Hoje -->
        <div class="box-funcao">
            <i class="fas fa-money-bill-wave"></i>
            <span>Saques Hoje</span>
            <span class="valor">R$ <?= number_format($saquesHoje, 2, ',', '.') ?></span>
        </div>

        <!-- Saldo dos Players -->
        <div class="box-funcao">
            <i class="fas fa-piggy-bank"></i>
            <span>Saldo dos Players</span>
            <span class="valor">R$ <?= number_format($saldoPlayers, 2, ',', '.') ?></span>
        </div>

        <!-- Despesas -->
        <div class="box-funcao <?= $despesas > 0 ? 'negativo' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Despesas</span>
            <span class="valor">R$ <?= number_format($despesas, 2, ',', '.') ?></span>
        </div>

        <!-- Lucro Bruto -->
        <div class="box-funcao <?= $lucroBruto < 0 ? 'negativo' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Lucro Bruto</span>
            <span class="valor">R$ <?= number_format($lucroBruto, 2, ',', '.') ?></span>
        </div>

        <!-- Lucro Líquido -->
        <div class="box-funcao <?= $lucroLiquido < 0 ? 'negativo' : '' ?>">
            <i class="fas fa-coins"></i>
            <span>Lucro Líquido</span>
            <span class="valor">R$ <?= number_format($lucroLiquido, 2, ',', '.') ?></span>
        </div>

    </div>
</div>