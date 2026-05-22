<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}
?>
<form method="GET" class="container-filtro">
    <input type="hidden" name="pagina" value="jogos">
    
    <input type="text" name="nome" placeholder="Nome do jogo" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">

   <select name="provedor">
    <option value="">Todos Provedores</option>
    <?php
    $provedores = ["PGSOFT","PRAGMATIC","SPRIBE","GALAXSYS ORIGINAL","HABANERO","PLAYSON","TOPTREND","DREAMTECH","EVOPLAY","BOOONGO","CQ9","REELKINGDOM","JETX ORIGINAL","DIGITAIN OFICIAL","OFICIAL - PRAGMATIC SLOTS","OFICIAL - PG SOFT","OFICIAL - CQ9","OFICIAL - FACHAI","OFICIAL - SPADE GAMING","OFICIAL - JDB","OFICIAL - GTF","OFICIAL - MICRO GAMING","OFICIAL - ALIZE SLOTS","OFICIAL - EVOPLAY","OFICIAL - EVOLUTION LIVE","OFICIAL - EZUGI","OFICIAL - WINFINITY","OFICIAL - NETENT","OFICIAL - NOLIMIT CITY","OFICIAL - BIG TIME GAMING","OFICIAL - RED TIGER","OFICIAL - YELLOWBAT","OFICIAL - QUEENMAKER","OFICIAL - 3OAKS","OFICIAL - SPRIBE","OFICIAL - ADVANTPLAY","OFICIAL - TADA","OFICIAL - HACKSAW","OFICIAL - ASKMESLOT","OFICIAL - BGAMING","OFICIAL - LIVE88","OFICIAL - CP GAMES","OFICIAL - TURBO GAMES","OFICIAL - EPICWIN","OFICIAL - BOOMING","OFICIAL - SPINOMENAL","OFICIAL - LIVE22","OFICIAL - CG","OFICIAL - AVIATRIX","OFICIAL - DREAMGAMING LIVE","OFICIAL - PRAGMATIC LIVE","FATPANDA","OFICIAL - FAT PANDA","POPOK"];

    $provedorSelecionado = $_GET['provedor'] ?? '';

    foreach ($provedores as $provedor) {
        // Comparação segura ignorando maiúsculas/minúsculas e espaços extras
        $selected = (strcasecmp(trim($provedorSelecionado), trim($provedor)) === 0) ? 'selected' : '';
        echo '<option value="' . htmlspecialchars($provedor) . '" ' . $selected . '>' . htmlspecialchars($provedor) . '</option>';
    }
    ?>
</select>

    <select name="ativo">
        <option value="">Todos Ativos</option>
        <option value="1" <?= isset($_GET['ativo']) && $_GET['ativo'] === '1' ? 'selected' : '' ?>>Somente Ativos</option>
        <option value="0" <?= isset($_GET['ativo']) && $_GET['ativo'] === '0' ? 'selected' : '' ?>>Somente Inativos</option>
    </select>

    <select name="original">
        <option value="">Todos Originais</option>
        <option value="1" <?= isset($_GET['original']) && $_GET['original'] === '1' ? 'selected' : '' ?>>Originais</option>
        <option value="0" <?= isset($_GET['original']) && $_GET['original'] === '0' ? 'selected' : '' ?>>Não Originais</option>
    </select>

    <select name="destacado">
        <option value="">Todos Destacados</option>
        <option value="1" <?= isset($_GET['destacado']) && $_GET['destacado'] === '1' ? 'selected' : '' ?>>Destacados</option>
        <option value="0" <?= isset($_GET['destacado']) && $_GET['destacado'] === '0' ? 'selected' : '' ?>>Não Destacados</option>
    </select>

    <button type="submit">Buscar</button>
</form>

<div class="botao-adicionar-jogo modalNovoJogo">Adicionar Jogo</div>

<?php
// Quantidade de registros por página
$registros_por_pagina = 10;

// Página atual via GET, padrão 1
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) ? (int)$_GET['pag'] : 1;
if ($pag < 1) $pag = 1;

// Monta os filtros
$where = [];
$params = [];

if (!empty($_GET['nome'])) {
    $where[] = 'game_name LIKE :nome';
    $params[':nome'] = '%' . $_GET['nome'] . '%';
}
if (!empty($_GET['provedor'])) {
    $where[] = 'game_provider LIKE :provedor';
    $params[':provedor'] = '%' . $_GET['provedor'] . '%';
}
if (isset($_GET['ativo']) && $_GET['ativo'] !== '') {
    $where[] = 'game_ativado = :ativo';
    $params[':ativo'] = (int)$_GET['ativo'];
}
if (isset($_GET['original']) && $_GET['original'] !== '') {
    $where[] = 'game_original = :original';
    $params[':original'] = (int)$_GET['original'];
}
if (isset($_GET['destacado']) && $_GET['destacado'] !== '') {
    $where[] = 'game_destacado = :destacado';
    $params[':destacado'] = (int)$_GET['destacado'];
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Conta o total de registros
try {
    $sql_total = "SELECT COUNT(*) FROM bet_jogos $whereSQL";
    $stmt_total = $pdo->prepare($sql_total);
    foreach ($params as $key => $value) {
        $stmt_total->bindValue($key, $value);
    }
    $stmt_total->execute();
    $total_registros = (int) $stmt_total->fetchColumn();
} catch (PDOException $e) {
    die("Erro ao contar registros: " . $e->getMessage());
}

// Calcula o total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Calcula o OFFSET para a consulta SQL
$offset = ($pag - 1) * $registros_por_pagina;

// Busca os jogos da página atual
try {
    $sql = "SELECT * FROM bet_jogos $whereSQL ORDER BY game_name ASC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $registros_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $jogos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar jogos: " . $e->getMessage());
}
?>
<div class="container-conteudo">
    <h2 class="titulo-jogos">Jogos</h2>
    <div class="jogos-table-wrapper">
        <table class="jogos-table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Provedor</th>
                    <th>Tipo</th>
                    <th>Original</th>
                    <th>Ativo</th>
                    <th>Destacado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jogos as $jogo): ?>
                    <tr>
                       <td><img src="../../includes/proxy.php?url=<?= urlencode($jogo['game_img']) ?>" class="jogo-img" alt="<?= htmlspecialchars($jogo['game_name']) ?>" onerror="this.onerror=null; this.src='../../../imagens/imageoff.png';" ></td>
                        <td class="jogo-nome"><?= htmlspecialchars($jogo['game_name']) ?></td>
                        <td><?= htmlspecialchars($jogo['game_provider']) ?></td>
                        <td><?= htmlspecialchars($jogo['game_type']) ?></td>
                        <td><?= $jogo['game_original'] ? 'Sim' : 'Não' ?></td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="<?= $jogo['id'] ?>" data-field="game_ativado" <?= $jogo['game_ativado'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="<?= $jogo['id'] ?>" data-field="game_destacado" <?= $jogo['game_destacado'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$limite_botoes = 5;

if ($total_paginas > 1) {
    echo '<div class="dashboard-pagination">';

    $inicio = max(1, $pag - floor($limite_botoes / 2));
    $fim = min($total_paginas, $inicio + $limite_botoes - 1);
    $inicio = max(1, $fim - $limite_botoes + 1);

    $query_params = $_GET;
    $query_params['pagina'] = 'jogos';

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

<div id="modalNovoJogo" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Adicionar jogo</h2>
        <div id="alerta-novojogo"></div>
        <form id="formnovojogo" action="php/novojogo.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_jogos'] ?? '' ?>">
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-font"></i>
                    <input type="text" name="game_name" placeholder="Nome do Jogo">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-barcode"></i>
                    <input type="text" name="game_code" placeholder="código do jogo">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-server"></i>
                    <select name="game_provider">
    <option value="">Selecione o Provedor do jogo</option>
    <?php
    $provedores = ["PGSOFT","PRAGMATIC","SPRIBE","GALAXSYS ORIGINAL","HABANERO","PLAYSON","TOPTREND","DREAMTECH","EVOPLAY","BOOONGO","CQ9","REELKINGDOM","JETX ORIGINAL","DIGITAIN OFICIAL","OFICIAL - PRAGMATIC SLOTS","OFICIAL - PG SOFT","OFICIAL - CQ9","OFICIAL - FACHAI","OFICIAL - SPADE GAMING","OFICIAL - JDB","OFICIAL - GTF","OFICIAL - MICRO GAMING","OFICIAL - ALIZE SLOTS","OFICIAL - EVOPLAY","OFICIAL - EVOLUTION LIVE","OFICIAL - EZUGI","OFICIAL - WINFINITY","OFICIAL - NETENT","OFICIAL - NOLIMIT CITY","OFICIAL - BIG TIME GAMING","OFICIAL - RED TIGER","OFICIAL - YELLOWBAT","OFICIAL - QUEENMAKER","OFICIAL - 3OAKS","OFICIAL - SPRIBE","OFICIAL - ADVANTPLAY","OFICIAL - TADA","OFICIAL - HACKSAW","OFICIAL - ASKMESLOT","OFICIAL - BGAMING","OFICIAL - LIVE88","OFICIAL - CP GAMES","OFICIAL - TURBO GAMES","OFICIAL - EPICWIN","OFICIAL - BOOMING","OFICIAL - SPINOMENAL","OFICIAL - LIVE22","OFICIAL - CG","OFICIAL - AVIATRIX","OFICIAL - DREAMGAMING LIVE","OFICIAL - PRAGMATIC LIVE","FATPANDA","OFICIAL - FAT PANDA","POPOK"];

    foreach ($provedores as $provedor) {
        echo "<option value=\"" . htmlspecialchars($provedor) . "\">" . htmlspecialchars($provedor) . "</option>";
    }
    ?>
</select>

                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-image"></i>
                    <input type="text" name="game_img" placeholder="URL da imagem do jogo">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-th-large"></i>
                    <select name="game_type">
                        <option value="">Selecione o Tipo do jogo</option>
                        <option value="Slots">Slots</option>
                        <option value="Crash">Crash</option>
                        <option value="Ao vivo">Ao Vivo</option>
                        <option value="Esporte">Esporte</option>
                    </select>
                </div>
            </div>   

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-code-branch"></i>
                    <select name="game_original">
                        <option value="">Selecione Clone ou Original</option>
                        <option value="1">Original</option>
                        <option value="0">Clone</option>
                    </select>
                </div>
            </div>

            <input type="submit" id="subNovoJogo" class="submit-button espacobutton" value="Cadastrar">
        </form>
    </div>
</div>