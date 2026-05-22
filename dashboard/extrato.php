<?php
if (!defined('IN_INDEX')) {
    header("Location: /dashboard/");
    exit();
}
?>
<?php
$usuario_id = $_SESSION['usuario_id'];

// Configurações da paginação
$registros_por_pagina = 10;
$pagina = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$inicio = ($pagina - 1) * $registros_por_pagina;

// Contar o total de registros para o usuário logado
$sql_total = "SELECT COUNT(*) AS total FROM bet_transacoes WHERE bet_usuario = :usuario_id";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_total->execute();
$total_registros = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

// Buscar os registros da página atual (montando LIMIT manualmente)
$sql = "SELECT id, bet_valor, bet_tipo, bet_status, bet_data 
        FROM bet_transacoes 
        WHERE bet_usuario = :usuario_id 
        ORDER BY bet_data DESC
        LIMIT $inicio, $registros_por_pagina";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
?>

<div class="container-conteudo">
    <div class="extrato-table-container">
        <h2 class="extrato-titulo">Extrato</h2>
        <table class="extrato-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt->rowCount() > 0): ?>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['bet_data']))) ?></td>
                            <td class="tipo"><?= htmlspecialchars($row['bet_tipo']) ?></td>
                            <td class="valor">R$ <?= number_format($row['bet_valor'], 2, ',', '.') ?></td>
                            <td>
                                <?php if ($row['bet_status'] === 'Aprovado'): ?>
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 4px; background-color: <?= $corPrincipal ?>; color: <?= $corTexto ?>;">
                                        <?= htmlspecialchars($row['bet_status']) ?>
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['bet_status']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma transação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
        // Paginação
        $total_paginas = ceil($total_registros / $registros_por_pagina);
        $limite_botoes = 5;

        if ($total_paginas > 1) {
            echo '<div class="extrato-pagination">';

            $inicio_pag = max(1, $pagina - floor($limite_botoes / 2));
            $fim_pag = min($total_paginas, $inicio_pag + $limite_botoes - 1);

            if ($inicio_pag > 1) {
                echo "<a href='?pagina=extrato&pg=1' class='pagination-btn'>1</a>";
                if ($inicio_pag > 2) echo "<span class='pagination-ellipsis'>...</span>";
            }

            for ($i = $inicio_pag; $i <= $fim_pag; $i++) {
                $classe = ($i == $pagina) ? 'active' : '';
                echo "<a href='?pagina=extrato&pg=$i' class='pagination-btn $classe'>$i</a>";
            }

            if ($fim_pag < $total_paginas) {
                if ($fim_pag < $total_paginas - 1) echo "<span class='pagination-ellipsis'>...</span>";
                echo "<a href='?pagina=extrato&pg=$total_paginas' class='pagination-btn'>$total_paginas</a>";
            }

            echo '</div>';
        }
        ?>