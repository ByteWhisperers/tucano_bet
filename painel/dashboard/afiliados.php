<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}
?>

<?php
$por_pagina = 10; // quantos afiliados por página
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) ? (int)$_GET['pag'] : 1;
if ($pag < 1) $pag = 1;
$offset = ($pag - 1) * $por_pagina;

// Total de afiliados (contagem distinta dos indicadores)
$total_stmt = $pdo->query("
    SELECT COUNT(DISTINCT u.id) 
    FROM bet_usuarios u
    JOIN bet_usuarios f ON f.bet_ref = u.id
");
$total_registros = $total_stmt->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta principal com LIMIT e OFFSET para paginação
$stmt = $pdo->prepare("
    SELECT 
        u.id AS indicador_id,
        u.bet_nome AS indicador_nome,
        u.bet_afiliado_por AS percentual,
        COUNT(f.id) AS total_afiliados
    FROM bet_usuarios u
    JOIN bet_usuarios f ON f.bet_ref = u.id
    GROUP BY u.id, u.bet_nome, u.bet_afiliado_por
    ORDER BY total_afiliados DESC
    LIMIT :limite OFFSET :offset
");
$stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-conteudo">
  <h2 class="titulo-afiliados">Afiliados</h2>
  <div class="afiliados-table-wrapper">
    <table class="afiliados-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>N Afiliados</th>
          <th>A Resgatar</th>
          <th>Resgatado</th>
          <th>% Atual</th>
          <th>Expandir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (count($registros) > 0):
          foreach ($registros as $row):
            $id = $row['indicador_id'];
            $nome = htmlspecialchars($row['indicador_nome']);
            $quantidade = $row['total_afiliados'];
            $percentual = $row['percentual'] ?? 0;

            // Soma dos bônus "A Receber"
            $stmt_bonus_areceber = $pdo->prepare("
                SELECT SUM(bt.bet_afiliado_bonus) 
                FROM bet_transacoes bt
                INNER JOIN bet_usuarios u ON bt.bet_usuario = u.id
                WHERE u.bet_ref = :id
                  AND bt.bet_tipo = 'Deposito'
                  AND bt.bet_status = 'Aprovado'
                  AND bt.bet_afiliado_status = 0
            ");
            $stmt_bonus_areceber->execute(['id' => $id]);
            $areceber = $stmt_bonus_areceber->fetchColumn() ?: 0;

            // Soma dos bônus "Recebido"
            $stmt_bonus_recebido = $pdo->prepare("
                SELECT SUM(bt.bet_afiliado_bonus) 
                FROM bet_transacoes bt
                INNER JOIN bet_usuarios u ON bt.bet_usuario = u.id
                WHERE u.bet_ref = :id
                  AND bt.bet_tipo = 'Deposito'
                  AND bt.bet_status = 'Aprovado'
                  AND bt.bet_afiliado_status = 1
            ");
            $stmt_bonus_recebido->execute(['id' => $id]);
            $recebido = $stmt_bonus_recebido->fetchColumn() ?: 0;
        ?>
        <tr>
          <td><?= $id ?></td>
          <td><?= $nome ?></td>
          <td><?= $quantidade ?></td>
          <td>R$ <?= number_format($areceber, 2, ',', '.') ?></td>
          <td>R$ <?= number_format($recebido, 2, ',', '.') ?></td>
          <td><?= $percentual ?>%</td>
          <td>
            <i class="fas fa-chevron-down btn-expandir" data-id="<?= $id ?>"></i>
          </td>
        </tr>
        <tr class="linha-expandida" id="expandir-<?= $id ?>" style="display: none;">
          <td colspan="7">
            <div class="conteudo-expandido">
              <div class="scroll-expandido">
                <p><strong>Indicações de <?= $nome ?>:</strong></p>
                <table class="tabela-indicados">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Total Depositado</th>
                      <th>Data de Cadastro</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $stmt_indicados = $pdo->prepare("
                      SELECT 
                          u.bet_nome, 
                          u.bet_data,
                          COALESCE((
                              SELECT SUM(t.bet_valor)
                              FROM bet_transacoes t
                              WHERE t.bet_usuario = u.id 
                                AND t.bet_tipo = 'Deposito'
                                AND t.bet_status = 'Aprovado'
                          ), 0) AS total_depositado
                      FROM bet_usuarios u
                      WHERE u.bet_ref = :id
                      ORDER BY u.bet_data DESC
                    ");
                    $stmt_indicados->execute(['id' => $id]);
                    $indicados = $stmt_indicados->fetchAll(PDO::FETCH_ASSOC);

                    if ($indicados):
                      foreach ($indicados as $ind):
                        $nome_ind = htmlspecialchars($ind['bet_nome']);
                        $data_cad = date('d/m/Y H:i', strtotime($ind['bet_data']));
                        $valor = number_format($ind['total_depositado'], 2, ',', '.');
                  ?>
                      <tr>
                        <td><?= $nome_ind ?></td>
                        <td>R$ <?= $valor ?></td>
                        <td><?= $data_cad ?></td>
                      </tr>
                  <?php
                      endforeach;
                    else:
                      echo '<tr><td colspan="3">Nenhum indicado encontrado.</td></tr>';
                    endif;
                  ?>
                  </tbody>
                </table>
              </div>
            </div>
          </td>
        </tr>
        <?php
          endforeach;
        else:
        ?>
        <tr>
          <td colspan="7" style="text-align:center;">Você ainda não possui afiliados cadastrados.</td>
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

    $inicio_paginacao = max(1, $pag - floor($limite_botoes / 2));
    $fim_paginacao = min($total_paginas, $inicio_paginacao + $limite_botoes - 1);
    $inicio_paginacao = max(1, $fim_paginacao - $limite_botoes + 1);

    $query_params = $_GET;
    $query_params['pagina'] = 'afiliados'; // importante para manter o filtro da página

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

<script>
  document.querySelectorAll('.btn-expandir').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const linha = document.getElementById('expandir-' + id);
      const icone = btn;

      if (linha.style.display === 'none') {
        linha.style.display = '';
        icone.classList.remove('fa-chevron-down');
        icone.classList.add('fa-chevron-up');
      } else {
        linha.style.display = 'none';
        icone.classList.remove('fa-chevron-up');
        icone.classList.add('fa-chevron-down');
      }
    });
  });

  document.querySelectorAll('.btn-fechar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const linha = document.getElementById('expandir-' + id);
      const icone = document.querySelector(`.btn-expandir[data-id="${id}"]`);
      linha.style.display = 'none';
      icone.classList.remove('fa-chevron-up');
      icone.classList.add('fa-chevron-down');
    });
  });
</script>