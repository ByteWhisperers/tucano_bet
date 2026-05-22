<!-- Tabela HTML -->
<div class="container-conteudo">

<?php 
// =========================
// CONTADORES MARKETING
// =========================

// Total da fila (todas campanhas)
$totalEmails = $pdo->query("
    SELECT COUNT(*) 
    FROM bet_marketing_envios
")->fetchColumn();

// Total enviados
$totalEnviados = $pdo->query("
    SELECT COUNT(*) 
    FROM bet_marketing_envios 
    WHERE bet_status = 'enviado'
")->fetchColumn();

// Total pendentes
$pendentes = $pdo->query("
    SELECT COUNT(*) 
    FROM bet_marketing_envios 
    WHERE bet_status = 'pendente'
")->fetchColumn();
?>

<!-- STATUS -->
<div class="marketing-status">
    <div class="box">
        Total
        <b><?= $totalEmails ?></b>
    </div>

    <div class="box enviado">
        Enviados
        <b><?= $totalEnviados ?></b>
    </div>

    <div class="box pendente">
        Pendentes
        <b><?= $pendentes ?></b>
    </div>
</div>

<div class="botao-adicionar-campanha modalEmailMarketing">Adicionar Campanha</div>


    <h2 class="titulo-marketing">Email Marketing</h2>

    <div class="emailmarketing-table-wrapper">
        <table class="emailmarketing-table">
            <thead>
                <tr>
                    <th>ID Campanha</th>
                    <th>Nome Campanha</th>
                    <th>Data de Criação</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody>
                <?php

// Página atual
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) ? (int)$_GET['pag'] : 1;

// Quantidade por página
$por_pagina = 10;

// Offset
$offset = ($pag - 1) * $por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) FROM bet_marketing_config";
$total_registros = $pdo->query($sql_total)->fetchColumn();

// Total de páginas
$total_paginas = ceil($total_registros / $por_pagina);

        $sql = "SELECT bet_id, bet_nome_campanha, bet_criada_em
        FROM bet_marketing_config
        ORDER BY bet_criada_em DESC
        LIMIT :limite OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();


                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tr>
                            <td><?= $row['bet_id']; ?></td>
                            <td><?= htmlspecialchars($row['bet_nome_campanha']); ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['bet_criada_em'])); ?></td>
                            <td>
<button
    class="btn-excluir-campanha"
    data-id="<?= $row['bet_id']; ?>"
    data-nome="<?= htmlspecialchars($row['bet_nome_campanha'], ENT_QUOTES); ?>"
    title="Excluir campanha" >
    <i class="fa-solid fa-trash"></i>
</button>

                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4">Nenhuma campanha no momento.</td>
                    </tr>
                    <?php
                }
                ?>
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
    $query_params['pagina'] = 'email_marketing'; // importante

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

<div id="modalExcluirCampanha" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Confirmar exclusão</h2>
        <div id="alerta-excluircampanha"></div>
        <form id="formexcluircampanha" action="php/excluircampanha.php">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_excluircampanha'] ?? '' ?>"> 
            
            <input type="hidden" id="excluir-campanha-id" name="campanha_id">

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-bullhorn"></i>
                    <span id="excluir-campanha-nome" class="input-fake"></span>
                </div>
            </div>

         <input type="submit" id="subExcluirCampanha" class="submit-button espacobutton" value="Excluir Campanha">
        </form>
    </div>
    
</div>


<div id="modalEmailMarketing" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Email Marketing</h2>
        <div id="alerta-emailmarketing"></div>
        <form id="formemailmarketing" action="php/emailmarketing.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_emailmarketing'] ?? '' ?>">

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-bullhorn"></i>
                    <input type="text" name="nomecampanha" placeholder="Nome da Campanha" >
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope-open-text"></i>
                    <input type="text" name="assuntoemail" placeholder="Assunto do E-mail" >
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-pencil-alt"></i>
                    <textarea id="mensagem" name="mensagem" rows="4" style="height: 60px; resize: none;" placeholder="Mensagem (HTML ou texto)"></textarea>
                </div>
            </div>

            <input type="submit" id="subEmailMarketing" class="submit-button espacobutton" value="Criar Campanha">
        </form>
<div class="link-modal">
    <p>Use {{nome}} e {{email}} para personalizar a mensagem com os dados de cada usuário</p>
    <p>Exemplo de uso: Olá {{nome}}, tudo bem? Seu e-mail é {{email}}</p>
</div>
    </div>
</div>