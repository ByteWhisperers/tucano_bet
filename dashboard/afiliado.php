<?php
if (!defined('IN_INDEX')) {
    header("Location: /dashboard/");
    exit();
}
?>

<div class="container-conteudo">
  <h2 class="afiliado-titulo">Programa de afliados</h2>

  <div class="afiliado-info">
      <p>
      O programa de afiliados <strong><?= $NomeSite ?></strong> recompensa seus afiliados com uma comissão de <strong><?= $porcentagem ?>%</strong> sobre cada depósito confirmado realizado pelos seus indicados. Os valores acumulados ficam disponíveis no painel de afiliado e podem ser resgatados a qualquer momento. Ao clicar no botão <strong>Resgatar</strong>, o valor é creditado automaticamente no seu saldo de retirada, permitindo que você solicite o saque a qualquer momento.
      </p>

     <div class="afiliado-content-link">
  <div class="form-row">
    <div class="input-icon">
      <i class="fas fa-link"></i>
      <input type="text" id="link-afiliado" value="https://<?= $Site ?>/?modal=cadastro&ref=<?= $_SESSION['usuario_id']; ?>" readonly>
    </div>
  </div>
</div>
<button id="botao-copiar" class="submit-button" onclick="copiarLink()">Copiar Link</button>

  </div>

  <script>
function copiarLink() {
  var input = document.getElementById('link-afiliado');
  if (!input) return;

  input.select();
  input.setSelectionRange(0, 99999); // para mobile

  document.execCommand('copy');

  var botao = document.getElementById('botao-copiar');
  if (!botao) return;

  botao.textContent = 'Link copiado!';

  setTimeout(function() {
    botao.textContent = 'Copiar Link';
  }, 2000);
}
</script>


<?php
// Buscar os indicados do usuário logado
$stmtInd = $pdo->prepare("SELECT id, bet_nome FROM bet_usuarios WHERE bet_ref = :ref");
$stmtInd->execute(['ref' => $_SESSION['usuario_id']]);
$indicados = $stmtInd->fetchAll(PDO::FETCH_ASSOC);

$transacoes = [];
$total_registros = 0;
$registros_por_pagina = 10;
$pagina = isset($_GET['pg']) && is_numeric($_GET['pg']) ? (int)$_GET['pg'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registros_por_pagina;

if ($indicados && count($indicados) > 0) {
    $idsIndicados = array_column($indicados, 'id');
    $placeholders = implode(',', array_fill(0, count($idsIndicados), '?'));

    // Contar total de registros
    $sqlCount = "SELECT COUNT(*) 
                 FROM bet_transacoes 
                 WHERE bet_usuario IN ($placeholders)
                   AND bet_status = 'Aprovado'
                   AND bet_tipo = 'Deposito'";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($idsIndicados);
    $total_registros = (int)$stmtCount->fetchColumn();

    // Buscar registros paginados
    $sql = "SELECT t.*, u.bet_nome 
            FROM bet_transacoes t 
            INNER JOIN bet_usuarios u ON t.bet_usuario = u.id 
            WHERE t.bet_usuario IN ($placeholders)
              AND t.bet_status = 'Aprovado'
              AND t.bet_tipo = 'Deposito'
            ORDER BY t.id DESC
            LIMIT $registros_por_pagina OFFSET $offset";
    $stmtTrans = $pdo->prepare($sql);
    $stmtTrans->execute($idsIndicados);
    $transacoes = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="afiliado-table-container">
  <table class="afiliado-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Depósito</th>
        <th>Bônus</th>
        <th>Resgatar</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$indicados || count($indicados) === 0): ?>
        <tr>
          <td colspan="5" style="text-align:center;">Ainda não há indicados.</td>
        </tr>
      <?php elseif (!$transacoes || count($transacoes) === 0): ?>
        <tr>
          <td colspan="5" style="text-align:center;">Seus indicados ainda não realizaram depósitos aprovados.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($transacoes as $trans): ?>
          <tr>
            <td><?= htmlspecialchars($trans['id']) ?></td>
            <td><?= htmlspecialchars(
              ucfirst(strtolower(explode(' ', trim($trans['bet_nome']))[0])) . ' ' .
              ucfirst(strtolower(end(explode(' ', trim($trans['bet_nome'])))))
            ) ?></td>
            <td>R$ <?= number_format($trans['bet_valor'], 2, ',', '.') ?></td>
            <td class="valor">
    <?php if ($trans['bet_afiliado_status'] == 0): ?>
      R$ <?= number_format($trans['bet_valor'] * ($porcentagem / 100), 2, ',', '.') ?>
    <?php else: ?>
      R$ <?= number_format($trans['bet_afiliado_bonus'], 2, ',', '.') ?>
    <?php endif; ?>
            </td>
            <td>
    <?php if ($trans['bet_afiliado_status'] == 0): ?>
            <button 
  class="resgatar" 
  data-id="<?= $trans['id'] ?>"
  data-valor="<?= number_format($trans['bet_valor'] * ($porcentagem / 100), 2, ',', '.') ?>"
  data-nome="<?= htmlspecialchars(
    ucfirst(strtolower(explode(' ', trim($trans['bet_nome']))[0])) . ' ' .
    ucfirst(strtolower(end(explode(' ', trim($trans['bet_nome'])))))
  ) ?>">Resgatar</button>
    <?php else: ?>
      Resgatado
    <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
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
                echo "<a href='?pagina=afiliado&pg=1' class='pagination-btn'>1</a>";
                if ($inicio_pag > 2) echo "<span class='pagination-ellipsis'>...</span>";
            }

            for ($i = $inicio_pag; $i <= $fim_pag; $i++) {
                $classe = ($i == $pagina) ? 'active' : '';
                echo "<a href='?pagina=afiliado&pg=$i' class='pagination-btn $classe'>$i</a>";
            }

            if ($fim_pag < $total_paginas) {
                if ($fim_pag < $total_paginas - 1) echo "<span class='pagination-ellipsis'>...</span>";
                echo "<a href='?pagina=afiliado&pg=$total_paginas' class='pagination-btn'>$total_paginas</a>";
            }

            echo '</div>';
        }
        ?>
</div>

<div id="modalAfiliados" class="modal">
  <div class="modal-content">
    <span class="close-modal"><i class="fas fa-times"></i></span>
    <h2>Resgatar bônus</h2>
    <div id="alerta-afiliados"></div>

    <form id="formafiliados" action="php/afiliados.php">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_afiliados'] ?? '' ?>">
      <input type="hidden" name="id_transacoes" id="afiliado-id" value="">

      <div class="form-row">
        <div class="input-icon">
          <i class="fas fa-user"></i>
          <input type="text" id="afiliado-nome" value="" readonly>
        </div>
      </div>

      <div class="form-row">
        <div class="input-icon">
          <i class="fas fa-gift"></i>
          <input type="text" id="afiliado-bonus" value="" readonly>
        </div>
      </div>
      <input type="submit" id="subAfiliados" class="submit-button dados" value="Resgatar">
    </form>
  </div>
</div>

<script>
const overlay = document.getElementById('overlay'); // seu overlay
const modalAfiliados = document.getElementById('modalAfiliados');
const closeModalButton = modalAfiliados.querySelector('.close-modal');
const modalAfiliadoButton = document.querySelectorAll('.resgatar');

modalAfiliadoButton.forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        const id = this.dataset.id;
        const nome = this.dataset.nome;
        const valor = this.dataset.valor;

        const inputId = document.getElementById('afiliado-id');
        inputId.value = id;
        inputId.setAttribute('value', id);

        const inputNome = document.getElementById('afiliado-nome');
        inputNome.value = nome;
        inputNome.setAttribute('value', nome);

        const inputBonus = document.getElementById('afiliado-bonus');
        inputBonus.value = 'R$ ' + valor;
        inputBonus.setAttribute('value', 'R$ ' + valor);

        // Abre modal e overlay
        modalAfiliados.classList.add('show');
        if (overlay) overlay.classList.add('show');

    });
});

function closeModal() {
    // Esconde alerta se visível
    const alerta = document.getElementById('alerta-afiliados');
    if (alerta && alerta.style.display !== 'none') {
        alerta.style.display = 'none';
    }

    // Fecha modal e overlay
    modalAfiliados.classList.remove('show');
    if (overlay) overlay.classList.remove('show');

    // Reseta formulário
    const form = modalAfiliados.querySelector('form');
    if (form) form.reset();
}

closeModalButton.addEventListener('click', closeModal);

if (overlay) {
    overlay.addEventListener('click', closeModal);
}
</script>