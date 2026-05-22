<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}

// Configuração de paginação
$registros_por_pagina = 10; 
$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$inicio = ($pag - 1) * $registros_por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) FROM bet_transacoes WHERE bet_tipo = 'Deposito'";
$total_stmt = $pdo->prepare($sql_total);
$total_stmt->execute();
$total_registros = $total_stmt->fetchColumn();

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal
$sql = "SELECT 
            t.id AS id_transacao,
            t.bet_valor,
            t.bet_data,
            t.bet_status,
            u.bet_nome
        FROM bet_transacoes t
        INNER JOIN bet_usuarios u ON t.bet_usuario = u.id
        WHERE t.bet_tipo = 'Deposito'
        ORDER BY t.id DESC
        LIMIT :inicio, :registros";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':registros', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();

// Pegando todos os depósitos para poder verificar depois
$depositos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-conteudo">
  <h2 class="titulo-depositos">Depósitos</h2>
  <div class="depositos-table-wrapper">
    <table class="depositos-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Valor</th>
          <th>Data</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
  <?php if (empty($depositos)): ?>
    <tr>
      <td colspan="5">Você ainda não possui depósitos cadastrados.</td>
    </tr>
  <?php else: ?>
    <?php foreach ($depositos as $row): ?>
      <?php
        $nomes = explode(' ', trim($row['bet_nome']));
        $primeiro = ucfirst(strtolower($nomes[0]));
        $ultimo = count($nomes) > 1 ? ucfirst(strtolower(end($nomes))) : '';
        $nome_formatado = $ultimo ? $primeiro . ' ' . $ultimo : $primeiro;
        $data_formatada = date('d/m/Y H:i:s', strtotime($row['bet_data']));
      ?>
      <tr>
        <td><?= htmlspecialchars($row['id_transacao']) ?></td>
        <td><?= htmlspecialchars($nome_formatado) ?></td>
        <td>R$ <?= number_format($row['bet_valor'], 2, ',', '.') ?></td>
        <td><?= $data_formatada ?></td>
        <td class="<?= $row['bet_status'] === 'Aprovado' ? 'status-aprovado' : 'status-pendente' ?>">
          <i class="fas <?= $row['bet_status'] === 'Aprovado' ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        </td>
      </tr>
    <?php endforeach; ?>
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
    $query_params['pagina'] = 'depositos'; // importante

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
