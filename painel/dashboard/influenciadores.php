<?php 
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}

$registrosPorPagina = 10;
$pag = isset($_GET['pag']) && is_numeric($_GET['pag']) && $_GET['pag'] > 0 ? (int)$_GET['pag'] : 1;
$offset = ($pag - 1) * $registrosPorPagina;

function formatarPrimeiroEUltimoNome($nomeCompleto) {
    $nomeCompleto = trim($nomeCompleto);
    if (empty($nomeCompleto)) return '';
    $partes = preg_split('/\s+/', $nomeCompleto);
    if (count($partes) === 1) return ucfirst(mb_strtolower($partes[0]));
    $primeiro = ucfirst(mb_strtolower($partes[0]));
    $ultimo = ucfirst(mb_strtolower(end($partes)));
    return $primeiro . ' ' . $ultimo;
}

// === BUSCA POR CPF/EMAIL ===
$buscaResultados = [];
$filtroAplicado = false;

if (!empty($_GET['emailcpf']) && !empty($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
    $valor = trim($_GET['emailcpf']);
    if ($tipo === 'email') {
        $stmt = $pdo->prepare("SELECT id, bet_nome, bet_email, bet_cpf, bet_influenciador 
                               FROM bet_usuarios WHERE bet_email = :valor LIMIT 20");
    } else {
        $stmt = $pdo->prepare("SELECT id, bet_nome, bet_email, bet_cpf, bet_influenciador 
                               FROM bet_usuarios WHERE bet_cpf = :valor LIMIT 20");
    }
    $stmt->execute([':valor' => $valor]);
    $buscaResultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $filtroAplicado = true;
}

// === LISTA FIXA DE INFLUENCIADORES ===
$totalInfluenciadores = $pdo->query("SELECT COUNT(*) FROM bet_usuarios WHERE bet_influenciador = 1")->fetchColumn();
$total_paginas = ceil($totalInfluenciadores / $registrosPorPagina);

$stmt = $pdo->prepare("SELECT id, bet_nome, bet_email, bet_cpf, bet_influenciador 
                       FROM bet_usuarios 
                       WHERE bet_influenciador = 1 
                       ORDER BY id DESC 
                       LIMIT :limite OFFSET :offset");
$stmt->bindValue(':limite', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$influenciadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- FORMULÁRIO DE BUSCA -->
<form method="GET" class="container-filtro" action="/painel/dashboard/">
  <input type="hidden" name="pagina" value="influenciadores">
  <input type="text" name="emailcpf" placeholder="Digite e-mail ou CPF" value="<?= htmlspecialchars($_GET['emailcpf'] ?? '') ?>">
  <select name="tipo">
      <option value="">Tipo da busca</option>
      <option value="email" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'email') ? 'selected' : '' ?>>E-mail</option>
      <option value="cpf" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'cpf') ? 'selected' : '' ?>>CPF</option>
  </select>
  <button type="submit">Buscar</button>
</form>

<!-- RESULTADOS DA BUSCA -->
<?php if ($filtroAplicado): ?>
  <div class="container-conteudo">
    <h2 class="titulo-influenciadores">Busca</h2>
    <div class="influenciadores-table-wrapper">
    <table class="influenciadores-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>CPF</th>
          <th>Influenciador</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($buscaResultados)): ?>
          <tr><td colspan="5">Nenhum resultado encontrado.</td></tr>
        <?php else: ?>
          <?php foreach ($buscaResultados as $usuario): ?>
            <tr>
              <td><?= $usuario['id'] ?></td>
              <td><?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?></td>
              <td><?= htmlspecialchars($usuario['bet_email']) ?></td>
              <td><?= htmlspecialchars($usuario['bet_cpf']) ?></td>
              <td>
                <label class="switch-influenciador">
                  <input type="checkbox"
                         class="toggle-status-influenciador"
                         data-id="<?= $usuario['id'] ?>"
                         data-field="bet_influenciador"
                         <?= $usuario['bet_influenciador'] == 1 ? 'checked' : '' ?>>
                  <span class="slider-influenciador"></span>
                </label>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  </div>
<?php endif; ?>

<!-- LISTA FIXA DE INFLUENCIADORES -->
<div class="container-conteudo">
  <h2 class="titulo-influenciadores">Influenciadores</h2>
  <div class="influenciadores-table-wrapper">
  <table class="influenciadores-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>CPF</th>
        <th>Influenciador</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($influenciadores)): ?>
        <tr><td colspan="5">Nenhum influenciador ativo.</td></tr>
      <?php else: ?>
        <?php foreach ($influenciadores as $usuario): ?>
          <tr>
            <td><?= $usuario['id'] ?></td>
            <td><?= htmlspecialchars(formatarPrimeiroEUltimoNome($usuario['bet_nome'])) ?></td>
            <td><?= htmlspecialchars($usuario['bet_email']) ?></td>
            <td><?= htmlspecialchars($usuario['bet_cpf']) ?></td>
            <td>
              <label class="switch-influenciador">
                <input type="checkbox"
                       class="toggle-status-influenciador"
                       data-id="<?= $usuario['id'] ?>"
                       data-field="bet_influenciador"
                       <?= $usuario['bet_influenciador'] == 1 ? 'checked' : '' ?>>
                <span class="slider-influenciador"></span>
              </label>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
  </div>


  <!-- Paginação -->
  <?php if ($total_paginas > 1): ?>
    <div class="dashboard-pagination">
      <?php 
        $limite_botoes = 5;
        $inicio = max(1, $pag - floor($limite_botoes / 2));
        $fim = min($total_paginas, $inicio + $limite_botoes - 1);
        $inicio = max(1, $fim - $limite_botoes + 1);

        for ($i = $inicio; $i <= $fim; $i++):
      ?>
        <a href="?pagina=influenciadores&pag=<?= $i ?>" class="pagination-btn <?= $i == $pag ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>



<!-- SCRIPTS -->
<script>
// Toggle de influenciador via AJAX
document.querySelectorAll('.toggle-status-influenciador').forEach(input => {
    input.addEventListener('change', () => {
        const id = input.dataset.id;
        const field = input.dataset.field;
        const status = input.checked ? 1 : 0;

        const payload = {id, field, status};

        fetch('php/influenciadores.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(resp => {
            if (!resp.sucesso) {
                alert('Erro ao atualizar status');
                input.checked = !input.checked; // reverte toggle
            } else {
                // Atualiza a página após a alteração
                location.reload();
            }
        })
        .catch(() => {
            alert('Erro ao conectar com o servidor');
            input.checked = !input.checked; // reverte toggle
        });
    });
});
</script>