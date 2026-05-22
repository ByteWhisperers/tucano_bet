<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}
?>
<?php
// Configuração de paginação
$registros_por_pagina = 10; 
$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$inicio = ($pag - 1) * $registros_por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) FROM bet_transacoes WHERE bet_tipo = 'Retirada'";
$total_stmt = $pdo->prepare($sql_total);
$total_stmt->execute();
$total_registros = $total_stmt->fetchColumn();

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal com ordenação: pendentes primeiro
$sql = "SELECT 
            t.id AS id_transacao,
            t.bet_valor,
            t.bet_data,
            t.bet_status,
            u.bet_nome,
            u.bet_cpf
        FROM bet_transacoes t
        INNER JOIN bet_usuarios u ON t.bet_usuario = u.id
        WHERE t.bet_tipo = 'Retirada'
        ORDER BY 
            (t.bet_status = 'Aprovado') ASC,  -- Pendentes primeiro
            t.id DESC
        LIMIT :inicio, :registros";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':registros', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
?>

<!-- Tabela HTML -->
<div class="container-conteudo">
    <h2 class="titulo-pagamentos">Pagamentos</h2>
    <div class="pagamentos-table-wrapper">
        <table class="pagamentos-table">
            <thead>
                <tr>
                    <th>ID Transação</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
<?php if ($stmt->rowCount() > 0): ?>
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <?php
            $isAprovado  = $row['bet_status'] === 'Aprovado';
            $isCancelado = $row['bet_status'] === 'Cancelado';

    $nomes = explode(' ', trim($row['bet_nome']));
    $primeiro = ucfirst(strtolower($nomes[0]));
    $ultimo = ucfirst(strtolower(end($nomes)));
    $nome_formatado = $primeiro . ' ' . $ultimo;
        ?>
        <tr>
            <td><?= htmlspecialchars($row['id_transacao']) ?></td>
            <td><?= htmlspecialchars($nome_formatado) ?></td>
            <td><?= htmlspecialchars($row['bet_cpf']) ?></td>
            <td>R$ <?= number_format($row['bet_valor'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['bet_data']) ?></td>
            <td>
                <label class="switch-pagamento">
                    <input 
                        type="checkbox" 
                        class="toggle-status-pagamento modalConfirmacao <?= $isCancelado ? 'cancelado' : '' ?>" 
                        data-id="<?= $row['id_transacao'] ?>" 
                        data-field="bet_status"
                        data-nome="<?= htmlspecialchars($row['bet_nome']) ?>"
                        data-cpf="<?= htmlspecialchars($row['bet_cpf']) ?>"
                        data-valor="<?= number_format($row['bet_valor'], 2, ',', '.') ?>"
                        <?= $isAprovado ? 'checked' : '' ?>
                        <?= ($isAprovado || $isCancelado) ? 'disabled' : '' ?>
                    >
                    <span class="slider-pagamento"></span>
                </label>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
        <tr>
            <td colspan="6">Nenhum pagamento no momento.</td>
        </tr>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
<?php
$limite_botoes = 5;

if ($total_paginas > 1) {
    echo '<div class="dashboard-pagination">';

    $inicio = max(1, $pag - floor($limite_botoes / 2));
    $fim = min($total_paginas, $inicio + $limite_botoes - 1);
    $inicio = max(1, $fim - $limite_botoes + 1);

    $query_params = $_GET;
    $query_params['pagina'] = 'pagamentos'; // importante

    if ($inicio > 1) {
        $query_params['pag'] = 1;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn'>1</a>";
        if ($inicio > 2) echo "<span class='pagination-ellipsis'>...</span>";
    }

    for ($i = $inicio; $i <= $fim; $i++) {
        $classe = ($i == $pag) ? 'active' : '';
        $query_params['pag'] = $i;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn $classe'>$i</a>";
    }

    if ($fim < $total_paginas) {
        if ($fim < $total_paginas - 1) echo "<span class='pagination-ellipsis'>...</span>";
        $query_params['pag'] = $total_paginas;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn'>$total_paginas</a>";
    }

    echo '</div>';
}
?>

<!-- Modal de confirmação -->
<div id="modalConfirmacao" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Confirmação</h2>
    <div id="alerta-confirmar"></div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-user"></i>
    <input type="text" id="input-nome" name="nome" readonly>
  </div>
</div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-id-card"></i>
    <input type="text" id="input-cpf" name="cpf" readonly>
  </div>
</div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-dollar-sign"></i>
    <input type="text" id="input-valor" name="valor" readonly>
  </div>
</div>

    <form id="formconfirmar" action="php/confirmar.php">
      <input type="hidden" name="extrato_id" id="extrato-id">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_confirmar'] ?? '' ?>">
      <input type="submit" id="subConfirmacao" class="submit-button espacobutton" value="Confirmar">
    </form>
    <button class="submit-button btn-cancelar espacobutton modalCancelamento">Cancelar Pagamento</button>
  </div>
</div>


<!-- Modal de cancelamento -->
<div id="modalCancelamento" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Cancelamento</h2>
    <div id="alerta-cancelar"></div>

    <div class="form-row">
      <div class="input-icon">
        <i class="fas fa-user"></i>
        <input type="text" id="cancelar-nome" name="nome" readonly>
      </div>
    </div>

    <div class="form-row">
      <div class="input-icon">
        <i class="fas fa-id-card"></i>
        <input type="text" id="cancelar-cpf" name="cpf" readonly>
      </div>
    </div>

    <div class="form-row">
      <div class="input-icon">
        <i class="fas fa-dollar-sign"></i>
        <input type="text" id="cancelar-valor" name="valor" readonly>
      </div>
    </div>

    <form id="formcancelar" action="php/cancelar.php" method="POST">
      <input type="hidden" name="extrato_id" id="cancelar-id">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_cancelar'] ?? '' ?>">
      <input type="submit" id="subCancelamento" class="submit-button espacobutton" value="Confirmar">
    </form>
  </div>
</div>