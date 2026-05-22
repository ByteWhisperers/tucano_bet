<?php
session_name('adm_session');
session_start();

// Gerar um token CSRF único para cada página
function gerar_token_csrf($form) {
    // Gerar o token baseado no nome do formulário para evitar colisão
    $token = bin2hex(random_bytes(32));
    $_SESSION["csrf_token_$form"] = $token;
    return $token;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Gerando o token para cada formulário
    $token_gerapix = gerar_token_csrf('gerapix');
    $token_playfiver = gerar_token_csrf('playfiver');
    $token_valores = gerar_token_csrf('valores');
    $token_facebook = gerar_token_csrf('facebook');
    $token_email = gerar_token_csrf('email');
    $token_logo = gerar_token_csrf('logo');
    $token_favicon = gerar_token_csrf('favicon');
    $token_slider = gerar_token_csrf('slider');
    $token_nomeurl = gerar_token_csrf('nomeurl');
    $token_cores = gerar_token_csrf('cores');
    $token_redes = gerar_token_csrf('redes');
    $token_jogos = gerar_token_csrf('jogos');
    $token_senha = gerar_token_csrf('senha');
    $token_confirmar = gerar_token_csrf('confirmar');
    $token_cancelar = gerar_token_csrf('cancelar');
    $token_saldomais = gerar_token_csrf('saldomais');
    $token_saldomenos = gerar_token_csrf('saldomenos');
    $token_porcentagemafiliado = gerar_token_csrf('porcentagemafiliado');
    $token_bonuscadastro = gerar_token_csrf('bonuscadastro');
    $token_afiliados = gerar_token_csrf('afiliados');
    $token_despesas = gerar_token_csrf('despesas');
    $token_bonusraspadinha = gerar_token_csrf('bonusraspadinha');
    $token_stopgames = gerar_token_csrf('stopgames');
    $token_esportes = gerar_token_csrf('esportes');
    $token_emailmarketing = gerar_token_csrf('emailmarketing');
    $token_excluircampanha = gerar_token_csrf('excluircampanha');
    $token_campocel = gerar_token_csrf('campocel');
}

include '../../includes/db.php';
require_once '../../includes/config.php';


// Se não está logado mas tem cookie, tenta autenticar via token
if (!isset($_SESSION['adm_id']) && isset($_COOKIE['auth_token_adm'])) {
    $token = $_COOKIE['auth_token_adm'];

    $stmt = $pdo->prepare("SELECT id, adm_status FROM bet_adm WHERE adm_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['adm_status'] == 1) {
        $_SESSION['adm_id'] = $usuario['id'];
    }else {
        // Apaga o cookie inválido e redireciona para login
        setcookie("auth_token_adm", "", time() - 3600, "/painel", "", true, true);
        header("Location: /painel/");
        exit;
    }
}

// Se não está logado (nem por sessão, nem por token), redireciona para login
if (!isset($_SESSION['adm_id'])) {
    header("Location: /painel/");
    exit;
}

// Já está logado, busca dados do usuário para uso no painel
$stmt = $pdo->prepare("SELECT id, adm_status FROM bet_adm WHERE id = ?");
$stmt->execute([$_SESSION['adm_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['adm_status'] != 1) {
    // Usuário não existe mais ou foi desativado: logout forçado
    setcookie("auth_token_adm", "", time() - 3600, "/painel", "", true, true);
    session_destroy();
    header("Location: /painel/");
    exit;
}

// A partir daqui, o usuário está autenticado e ativo
$id = $usuario['id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - <?= $NomeSite ?></title>
    <link rel="icon" type="image/png" href="../../imagens/<?= $Favicon ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="css/estilos.php">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>

<!-- Conteúdo dos modais -->
<div class="overlay" id="overlay"></div>	

<!-- Container topo -->
 <div class="top-bar">
    <div class="container">
        <div class="logo-menu">
            <i class="fas fa-bars menu-icon" onclick="openMenu()"></i>
            <div class="logo">
                <img src="../../imagens/<?= $Logo ?>">
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Menu -->
<div id="mySidebar" class="sidebar">
    <span class="close-btn" onclick="closeMenu()">×</span> <!-- Botão de fechar -->
    <a href="/painel/dashboard/"><i class="fas fa-chart-line"></i> Estatísticas</a>
    <a href="/painel/dashboard/?pagina=funcoes"><i class="fas fa-tools"></i> Funções</a>
    <a href="/painel/dashboard/?pagina=usuarios"><i class="fas fa-users-cog"></i> Usuários</a>
    <a href="/painel/dashboard/?pagina=influenciadores"><i class="fas fa-bullhorn"></i> Influenciadores</a>
    <a href="/painel/dashboard/?pagina=jogos"><i class="fas fa-gamepad"></i> Jogos</a>
    <a href="/painel/dashboard/?pagina=depositos"><i class="fas fa-hand-holding-usd"></i> Depósitos</a>
    <a href="/painel/dashboard/?pagina=pagamentos"><i class="fas fa-credit-card"></i> Pagamentos</a>
    <a href="/painel/dashboard/?pagina=despesas"><i class="fas fa-file-invoice-dollar"></i> Despesas</a>
    <a href="/painel/dashboard/?pagina=afiliados"><i class="fas fa-users"></i> Afiliados</a>
    <a href="/painel/dashboard/?pagina=email_marketing"><i class="fas fa-envelope"></i> Email Marketing</a>
    <a class="btn-senha"><i class="fas fa-user-lock"></i> Atualizar senha</a>
    <a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>

<div id="modalSenha" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Atualizar Senha</h2>
        <div id="alerta-senha"></div>
        <form id="formsenha" action="php/senha.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_senha'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
            <input type="password" name="senha" placeholder="Senha com no mínimo 8 caracteres">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
            <input type="password" name="confirmasenha" placeholder="Confirme a senha">
                </div>
            </div>
            <input type="submit" id="subSenha" class="submit-button senha" value="Atualizar">
        </form>
    </div>
</div>    

<div class="content-box">
	<?php 
            define('IN_INDEX', true);

            $pagina = $_GET["pagina"] ?? null;

    switch ($pagina) {
        case 'depositos':
            include "depositos.php";
            break;

        case 'pagamentos':
            include "pagamentos.php";
            break;

        case 'despesas':
            include "despesas.php";
            break;  

        case 'afiliados':
            include "afiliados.php";
            break;   

         case 'email_marketing':
            include "email_marketing.php";
            break;          
           
        case 'usuarios':
            include "usuarios.php";
            break;    

        case 'influenciadores':
            include "influenciadores.php";
            break;        

        case 'jogos':
            include "jogos.php";
            break;

        case 'funcoes':
            include "funcoes.php";
            break;

        default:
            include "dashboard.php";
            break;
    }
           ?>
</div>

<footer>
  <p>Todos os direitos reservados <?= $NomeSite ?> &copy; 2025</p>
</footer>

<script>
	// Função Upload de arquivos
    document.addEventListener('DOMContentLoaded', () => {
  const fileInputs = document.querySelectorAll('input[type="file"]');

  fileInputs.forEach(input => {
    const label = input.previousElementSibling;
    const span = label.querySelector('span');

    const originalText = span.textContent;
    span.dataset.originalText = originalText;

    input.addEventListener('change', () => {
      if (input.files.length > 0) {
        const nomesArquivos = Array.from(input.files).map(file => file.name).join(', ');
        span.textContent = nomesArquivos;
        span.classList.add('active');
      } else {
        span.textContent = originalText;
        span.classList.remove('active');
      }
    });
  });

  function limparInputsFile() {
    document.querySelectorAll('input[type="file"]').forEach(input => {
      input.value = '';
      const label = input.previousElementSibling;
      const span = label?.querySelector('span');
      if (span) {
        span.textContent = span.dataset.originalText || 'Escolher arquivo';
        span.classList.remove('active');
      }
    });
  }

  const overlay = document.getElementById('overlay');
  if (overlay) {
    overlay.addEventListener('click', limparInputsFile);
  }

  document.querySelectorAll('.close-modal').forEach(button => {
    button.addEventListener('click', limparInputsFile);
  });
});
</script>

<script>
$('.toggle-status').on('change', function () {
    const id = $(this).data('id');
    const field = $(this).data('field');
    const value = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: 'php/status_jogos.php',
        method: 'POST',
        data: { id: id, field: field, value: value },
        success: function(response) {
        },
        error: function() {
        }
    });
});
</script>

<script>
$('.toggle-status-usuario').on('change', function () {
    const id = $(this).data('id');
    const field = $(this).data('field');
    const value = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: 'php/status_usuarios.php',
        method: 'POST',
        data: { id: id, field: field, value: value },
        success: function(response) {
        },
        error: function() {
        }
    });
});
</script>

<script>
  // Seleciona todos os selects da página
  const selects = document.querySelectorAll('select');

  selects.forEach(select => {
    // Função que atualiza a cor conforme o valor selecionado
    function updateColor() {
      select.style.color = select.value === '' ? '#777' : '#fff';
    }

    // Executa ao carregar e ao mudar
    updateColor();
    select.addEventListener('change', updateColor);
  });
</script>

<script>
document.getElementById('togglePagamento').addEventListener('change', function() {
    document.getElementById('pagamento_auto').value = this.checked ? '1' : '0';
});
</script>

</body>
</html>