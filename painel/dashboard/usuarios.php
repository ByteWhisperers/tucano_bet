<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}

function formatarCelular($cel) {
    $cel = preg_replace('/\D/', '', $cel);

    if (strlen($cel) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $cel);
    }

    if (strlen($cel) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $cel);
    }

    return $cel;
}

$registrosPorPagina = 10;
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) && $_GET['pag'] > 0 ? (int)$_GET['pag'] : 1;
$offset = ($pag - 1) * $registrosPorPagina;

$whereClause = "";
$params = [];
$filtroAplicado = false;
$mostrarTodos = false;

if (isset($_GET['emailcpf']) || isset($_GET['tipo'])) {
    $emailcpf = trim($_GET['emailcpf'] ?? '');
    $tipo = $_GET['tipo'] ?? '';

    if ($emailcpf !== '') {
        if ($tipo !== 'cpf' && $tipo !== 'email') {
            $tipo = 'email';
        }

        $filtroAplicado = true;

        if ($tipo === 'email') {
            $whereClause = "WHERE bet_email = :valor";
            $params[':valor'] = $emailcpf;
        } else {
            $whereClause = "WHERE bet_cpf = :valor";
            $params[':valor'] = $emailcpf;
        }
    } else {
        if ($tipo === '') {
            $mostrarTodos = true;
        } else {
            $filtroAplicado = false;
        }
    }
}

// Conta total de registros
$sqlTotal = "SELECT COUNT(*) FROM bet_usuarios " . ($mostrarTodos ? "" : $whereClause);
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($params);
$totalRegistros = $stmtTotal->fetchColumn();
$total_paginas = ceil($totalRegistros / $registrosPorPagina);

// Busca registros da página
$sql = "
    SELECT id, bet_nome, bet_email, bet_cpf, bet_celular, bet_saldo, bet_afiliado_por, bet_status
    FROM bet_usuarios
    " . ($mostrarTodos ? "" : $whereClause) . "
    ORDER BY id DESC
    LIMIT :limite OFFSET :offset
";
$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limite', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatarPrimeiroEUltimoNome($nomeCompleto) {
    $nomeCompleto = trim($nomeCompleto);
    if (empty($nomeCompleto)) return '';

    $partes = preg_split('/\s+/', $nomeCompleto);

    if (count($partes) === 1) {
        return ucfirst(mb_strtolower($partes[0]));
    }

    $primeiro = ucfirst(mb_strtolower($partes[0]));
    $ultimo = ucfirst(mb_strtolower(end($partes)));

    return $primeiro . ' ' . $ultimo;
}
?>

<form method="GET" class="container-filtro" action="/painel/dashboard/">
  <input type="hidden" name="pagina" value="usuarios">
  <input type="text" name="emailcpf" placeholder="Digite e-mail ou CPF" value="<?= htmlspecialchars($_GET['emailcpf'] ?? '') ?>">
  <select name="tipo">
      <option value="">Tipo da busca</option>
      <option value="email" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'email') ? 'selected' : '' ?>>E-mail</option>
      <option value="cpf" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'cpf') ? 'selected' : '' ?>>CPF</option>
  </select>
  <button type="submit">Buscar</button>
</form>

<div class="container-conteudo">
  <h2 class="titulo-usuarios">Usuários</h2>
  <div class="usuarios-table-wrapper">
    <table class="jogos-table" style="width:100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>CPF / EMAIL / CEL</th>
          <th>Saldo</th>
          <th>% Afiliado</th>
          <th>Ativado</th>
        </tr>
      </thead>
      <tbody>
<?php
if ((isset($_GET['emailcpf']) || isset($_GET['tipo'])) && !$filtroAplicado && !$mostrarTodos):
?>
  <tr>
    <td colspan="6">Por favor, preencha o campo para realizar a busca.</td>
  </tr>
<?php elseif (empty($usuarios)): ?>
  <tr>
    <td colspan="6">
      <?php if ($filtroAplicado): ?>
        Nenhum usuário encontrado.
      <?php else: ?>
        Você ainda não possui usuários cadastrados.
      <?php endif; ?>
    </td>
  </tr>
<?php else: ?>
  <?php foreach ($usuarios as $usuario): ?>
    <tr>
      <td><?= htmlspecialchars($usuario['id']) ?></td>
      <td><?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?></td>
      <td>
  <?= htmlspecialchars($usuario['bet_cpf']) ?>
  <?php if (!empty($usuario['bet_email'])): ?>
    <br><?= htmlspecialchars($usuario['bet_email']) ?>
  <?php endif; ?>
<?php if (!empty($usuario['bet_celular'])): ?>
  <br><?= htmlspecialchars(formatarCelular($usuario['bet_celular'])) ?>
<?php endif; ?>
</td>

      <td>
        <div class="saldo-wrapper">
          <a class="modalSaldoMenosUsuario"
             data-id="<?= $usuario['id'] ?>"
             data-nome="<?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?>"
             data-cpf="<?= htmlspecialchars($usuario['bet_cpf']) ?>">
            <i class="fas fa-minus-circle icon-menor"></i>
          </a>
          <span class="saldo-valor">R$ <?= number_format($usuario['bet_saldo'], 2, ',', '.') ?></span>
          <a class="modalSaldoMaisUsuario"
             data-id="<?= $usuario['id'] ?>"
             data-nome="<?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?>"
             data-cpf="<?= htmlspecialchars($usuario['bet_cpf']) ?>">
            <i class="fas fa-plus-circle icon-maior"></i>
          </a>
        </div>
      </td>
      <td>
        <div class="percentual-wrapper">
          <span class="percentual-valor"><?= (int)$usuario['bet_afiliado_por'] ?>%</span>
          <a class="modalPorcentagemAfiliados"
             data-id="<?= $usuario['id'] ?>"
             data-nome="<?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?>"
             data-cpf="<?= htmlspecialchars($usuario['bet_cpf']) ?>">
            <i class="fas fa-edit icon-editar"></i>
          </a>
        </div>
      </td>
      <td>
        <label class="switch-usuario">
          <input type="checkbox"
                 class="toggle-status-usuario"
                 data-id="<?= $usuario['id'] ?>"
                 data-field="bet_status"
                 <?= $usuario['bet_status'] == 1 ? 'checked' : '' ?>>
          <span class="slider-usuario"></span>
        </label>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>
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
    $query_params['pagina'] = 'usuarios'; // Aqui trocamos jogos por usuarios

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

<!-- Modal -->
<div id="modalSaldoMaisUsuario" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Adicionar Saldo</h2>
    <div id="alerta-saldo-mais"></div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-user"></i>
    <input type="text" id="input-nome-mais" name="nome" readonly>
  </div>
</div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-id-card"></i>
    <input type="text" id="input-cpf-mais" name="cpf" readonly>
  </div>
</div>

    <form id="formsaldomais" action="php/saldomais.php">
      <input type="hidden" name="usuario_id" id="saldo-mais-id">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_saldomais'] ?? '' ?>">
      
      <div class="form-row">
          <div class="input-icon">
          <i class="fas fa-dollar-sign"></i>
          <input type="text" id="input-valor-mais" name="valor" placeholder="Valor do saldo">
          </div>
      </div>

      <input type="submit" id="subSaldoMais" class="submit-button espacobutton" value="Atualizar">
    </form>
  </div>
</div>

<div id="modalSaldoMenosUsuario" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Remover Saldo</h2>
    <div id="alerta-saldo-menos"></div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-user"></i>
    <input type="text" id="input-nome-menos" name="nome" readonly>
  </div>
</div>

<div class="form-row">
  <div class="input-icon">
    <i class="fas fa-id-card"></i>
    <input type="text" id="input-cpf-menos" name="cpf" readonly>
  </div>
</div>

    <form id="formsaldomenos" action="php/saldomenos.php">
      <input type="hidden" name="usuario_id" id="saldo-menos-id">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_saldomenos'] ?? '' ?>">
      
      <div class="form-row">
          <div class="input-icon">
          <i class="fas fa-dollar-sign"></i>
          <input type="text" id="input-valor-menos" name="valor" placeholder="Valor do saldo">
          </div>
      </div>

      <input type="submit" id="subSaldoMenos" class="submit-button espacobutton" value="Atualizar">
    </form>
  </div>
</div>

<div id="modalPorcentagemAfiliados" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Porcentagem do afiliado</h2>
    <div id="alerta-porcentagem-afiliado"></div>

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

    <form id="formporcentagemafiliado" action="php/porcentagemafiliado.php">
      <input type="hidden" name="usuario_id" id="porcentagem-afiliado-id">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_porcentagemafiliado'] ?? '' ?>">
      
      <div class="form-row"> 
  <div class="input-icon">
    <i class="fas fa-percentage"></i>
    <select name="porcentagem_afiliado" id="select-porcentagem">
      <option value="">Selecione a porcentagem</option>
      <option value="5">5%</option>
      <option value="10">10%</option>
      <option value="15">15%</option>
      <option value="20">20%</option>
      <option value="25">25%</option>
      <option value="30">30%</option>
      <option value="35">35%</option>
      <option value="40">40%</option>
      <option value="45">45%</option>
      <option value="50">50%</option>
      <option value="55">55%</option>
      <option value="60">60%</option>
      <option value="65">65%</option>
      <option value="70">70%</option>
      <option value="75">75%</option>
    </select>
  </div>
</div>
      <input type="submit" id="subPorcentagemAfiliados" class="submit-button espacobutton" value="Atualizar">
    </form>
  </div>
</div>