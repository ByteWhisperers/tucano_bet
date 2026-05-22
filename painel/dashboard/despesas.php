<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}
?>

<div class="botao-adicionar-despesa modalDespesas">Adicionar Despesa</div>

<?php
// Configuração de paginação
$registros_por_pagina = 10; 
$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
$inicio = ($pag - 1) * $registros_por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) FROM bet_despesas";
$total_stmt = $pdo->prepare($sql_total);
$total_stmt->execute();
$total_registros = $total_stmt->fetchColumn();

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal
$sql = "SELECT 
            id,
            bet_descricao,
            bet_valor,
            bet_data
        FROM bet_despesas
        ORDER BY bet_data DESC
        LIMIT :inicio, :registros";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':registros', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();

$despesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-conteudo">
  <h2 class="titulo-despesas">Despesas</h2>
  <div class="despesas-table-wrapper">
    <table class="despesas-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Descrição</th>
          <th>Valor</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
  <?php if (empty($despesas)): ?>
    <tr>
      <td colspan="4">Você ainda não possui despesas cadastradas.</td>
    </tr>
  <?php else: ?>
    <?php foreach ($despesas as $row): ?>
      <?php
        $descricao = htmlspecialchars($row['bet_descricao'] ?? '');
        $valor = number_format($row['bet_valor'], 2, ',', '.');
        $data_formatada = date('d/m/Y', strtotime($row['bet_data']));
      ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= $descricao ?></td>
        <td>R$ <?= $valor ?></td>
        <td><?= $data_formatada ?></td>
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

    $inicio_paginacao = max(1, $pag - floor($limite_botoes / 2));
    $fim_paginacao = min($total_paginas, $inicio_paginacao + $limite_botoes - 1);
    $inicio_paginacao = max(1, $fim_paginacao - $limite_botoes + 1);

    $query_params = $_GET;
    $query_params['pagina'] = 'despesas'; // importante para manter o filtro da página

    if ($inicio_paginacao > 1) {
        $query_params['pag'] = 1;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn'>1</a>";
        if ($inicio_paginacao > 2) echo "<span class='pagination-ellipsis'>...</span>";
    }

    for ($i = $inicio_paginacao; $i <= $fim_paginacao; $i++) {
        $classe = ($i == $pag) ? 'active' : '';
        $query_params['pag'] = $i;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn $classe'>$i</a>";
    }

    if ($fim_paginacao < $total_paginas) {
        if ($fim_paginacao < $total_paginas - 1) echo "<span class='pagination-ellipsis'>...</span>";
        $query_params['pag'] = $total_paginas;
        echo "<a href='?" . http_build_query($query_params) . "' class='pagination-btn'>$total_paginas</a>";
    }

    echo '</div>';
}
?>

<div id="modalDespesas" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Adicionar Despesa</h2>
        <div id="alerta-despesas"></div>
        <form id="formdespesas" action="php/despesas.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_despesas'] ?? '' ?>">
            
            <div class="form-row">
              <div class="input-icon">
                <i class="fas fa-align-left"></i>
                <input type="text" name="bet_descricao" placeholder="Descrição da despesa">
              </div>
            </div>

            <div class="form-row">
              <div class="input-icon">
                <i class="fas fa-dollar-sign"></i>
                <input type="text" id="valor-despesa" name="bet_valor" placeholder="Valor da despesa">
              </div>
            </div>

            <div class="form-row">
              <div class="input-icon">
                <i class="fas fa-calendar-alt"></i>
                <input type="text" id="bet_data" name="bet_data" placeholder="Data da despesa">
              </div>
            </div>


            <input type="submit" id="subDespesas" class="submit-button espacobutton" value="Cadastrar">
        </form>
    </div>
</div>

<!-- JS do Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

<script>
    flatpickr("#bet_data", {
        locale: "pt",
        dateFormat: "d/m/Y",
        allowInput: false,
         onReady: function(selectedDates, dateStr, instance) {
    instance._input.placeholder = "Data da despesa";
  }
    });
   flatpickr("#seletor", {
  monthSelectorType: "static", // impede dropdown do mês
  allowInput: false,
  onReady: function(selectedDates, dateStr, instance) {
    // Remove foco e clique do mês
    const month = instance.calendarContainer.querySelector(".flatpickr-monthDropdown-months");
    if (month) {
      month.style.pointerEvents = "none";
      month.style.cursor = "default";
    }

    // Remove foco e clique do ano (input numérico)
    const yearInput = instance.calendarContainer.querySelector(".numInputWrapper");
    if (yearInput) {
      yearInput.style.pointerEvents = "none";
      yearInput.style.cursor = "default";
    }
  }
});
</script>