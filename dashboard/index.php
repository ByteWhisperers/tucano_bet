<?php
session_start();

// Gerar um token CSRF único para cada página
function gerar_token_csrf($form) {
    // Gerar o token baseado no nome do formulário para evitar colisão
    $token = bin2hex(random_bytes(32));
    $_SESSION["csrf_token_$form"] = $token;
    return $token;
}

// Exemplo de como gerar para diferentes formulários
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Gerando o token para cada formulário
    $token_deposito = gerar_token_csrf('deposito');
    $token_retirada = gerar_token_csrf('retirada');
    $token_dados = gerar_token_csrf('dados');
    $token_senha = gerar_token_csrf('senha');
    $token_contato = gerar_token_csrf('contato');
    $token_afiliados = gerar_token_csrf('afiliados');
    $token_bonus = gerar_token_csrf('bonus');
    $token_raspadinha = gerar_token_csrf('raspadinha');
}

define('IN_INDEX', true);
require_once '../includes/db.php';
require_once '../includes/config.php';

// Se não estiver logado e tiver o cookie com token
if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];

    $stmt = $pdo->prepare("SELECT id, bet_status FROM bet_usuarios WHERE bet_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['bet_status'] == 1) {
        $_SESSION['usuario_id'] = $usuario['id'];
    } else {
        setcookie("auth_token", "", time() - 3600, "/");
        header("Location: /");
        exit;
    }
}

// Se ainda não estiver logado, redireciona
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /");
    exit;
} else {
    // Puxar os dados do usuário logado
    $stmt = $pdo->prepare("SELECT bet_nome, bet_email, bet_cpf, bet_saldo, bet_afiliado_por FROM bet_usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nome  = $usuario['bet_nome'];
        $email = $usuario['bet_email'];
        $cpf   = $usuario['bet_cpf'];
        $saldo = $usuario['bet_saldo'];
        $porcentagem = $usuario['bet_afiliado_por'];
    }

    // Puxar o TOTAL de bônus pendentes
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(bet_bonus_valor), 0) AS total_bonus FROM bet_bonus WHERE bet_usuario = ? AND bet_bonus_status = 0");
    $stmt->execute([$_SESSION['usuario_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $saldo_bonus = $result ? $result['total_bonus'] : 0;
    
$mostrar_raspadinha = false; // padrão

// Pega o status do último bônus Raspadinha do usuário
$stmt = $pdo->prepare("
    SELECT bet_bonus_status 
    FROM bet_bonus 
    WHERE bet_usuario = ? AND bet_bonus_tipo = 'Raspadinha'
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['usuario_id']]);
$bonus_raspadinha = $stmt->fetchColumn();

// Se não houver registro, considera como 0; se houver, considera 1
$bonus_raspadinha = ($bonus_raspadinha === false ? 0 : 1);

// Lógica de exibição da raspadinha
if ($bonus_raspadinha == 0 && $ChaveBonusRaspadinha == 1) {
    // chave 1 + bonus 0 = false
    $mostrar_raspadinha = false;
} elseif ($bonus_raspadinha == 0 && $ChaveBonusRaspadinha == 0) {
    // chave 0 + bonus 0 = true
    $mostrar_raspadinha = true;
} elseif ($bonus_raspadinha == 1 && $ChaveBonusRaspadinha == 1) {
    // chave 1 + bonus 1 = true
    $mostrar_raspadinha = true;
} else {
    // outros casos
    $mostrar_raspadinha = true;
}


}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $NomeSite ?> Cassino e Apostas Online com Bônus Exclusivos</title>
    <link rel="icon" type="image/png" href="../imagens/<?= $Favicon ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.php">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>const usuarioTemBonus = <?= $mostrar_raspadinha ? 'true' : 'false' ?>;</script>
    <script src="js/scripts.js"></script>
</head>
<body>

 <!-- Conteúdo dos termos -->
<div id="sidebartermo" class="sidebartermo">
    <span class="close-sidebartermo"><i class="fas fa-times"></i></span> 
    <div class="sidebartermo-content" id="sidebartermoContent"></div>
</div>

<!-- Sidebar Menu -->
<div id="mySidebar" class="sidebar">
    <span class="close-btn" onclick="closeMenu()">×</span>
    <a href="/dashboard/"><i class="fas fa-gamepad"></i> Jogos</a>
    <a href="/dashboard/?pagina=extrato"><i class="fas fa-file-invoice-dollar"></i> Extrato</a>
    <a class="btn-dados"><i class="fas fa-sync-alt"></i> Atualizar dados</a>
    <a class="btn-senha"><i class="fas fa-user-lock"></i> Atualizar senha</a>
    <a href="/dashboard/?pagina=afiliado"><i class="fas fa-users"></i> Afiliado</a>
    <a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>

<!-- Conteúdo dos modais -->
<div class="overlay" id="overlay"></div>

<!-- Modal Depósito -->
<div id="modalDeposito" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Depósito</h2>
        <div id="alerta-deposito"></div>
        <form id="formdeposito" action="php/deposito.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_deposito'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-dollar-sign"></i>
            <input type="text" id="deposito" name="deposito" class="currency" placeholder="Valor do depósito" inputmode="decimal">
                </div>
            </div>

            <input type="submit" id="subDeposito" class="submit-button gerar-pix" value="Gerar Pix">
        </form>
        <div class="msg-deposito"><p>O depósito mínimo é de R$ <strong><?php echo number_format($ValorDeposito, 2, ',', ''); ?></strong> reais.</p><p>O depósito máximo é de R$ <strong>10.000,00</strong> reais.</p></div>
    </div>
</div>

<div id="modalRetirada" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Retirada</h2>
        <div id="alerta-retirada"></div>
        <form id="formretirada" action="php/retirada.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_retirada'] ?? '' ?>">
            <p class="saldofomP">Seu saldo para retirada é de R$ <strong><?= number_format($saldo, 2, ',', '.'); ?></strong></p>
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-dollar-sign"></i>
                    <input type="text" id="retirada" name="retirada" class="currency" placeholder="Valor da retirada" inputmode="decimal">
                </div>
            </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" value="Titular: <?= ucwords(strtolower($nome)); ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" value="Chave PIX CPF: <?= $cpf ?>" readonly>
                    </div>
                </div>

          <input type="submit" id="subRetirada" class="submit-button saque" value="Retirar">

        </form>
        <div class="msg-retirada"><p>A retirada mínima é de R$ <strong><?php echo number_format($ValorRetirada, 2, ',', ''); ?></strong> reais.</p><p>A retirada máxima é de R$ <strong>10.000,00</strong> reais.</p></div>
    </div>
</div>

<div id="modalBonus" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Resgatar bônus</h2>
        <div id="alerta-bonus"></div>
        <form id="formbonus" action="php/bonus.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_bonus'] ?? '' ?>">
            
                <div class="form-row">
                    <div class="input-icon">
                    <i class="fas fa-dollar-sign"></i>
                    <input type="text" name="valorbonus" value="R$ <?php echo number_format($saldo_bonus ?? 0, 2, ',', ''); ?>" readonly>
                </div>
                </div>
          <input type="submit" id="subBonus" class="submit-button bonus" value="Resgatar">
        </form>
    </div>
</div>

<div id="modalDados" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Atualizar dados</h2>
        <div id="alerta-dados"></div>
        <form id="formdados" action="php/dados.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_dados'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" value="<?= ucwords(strtolower($nome)); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" value="CPF: <?= $cpf ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" name="emaildados" value="<?= $email ?>" placeholder="Email">
                </div>
            </div>
            <input type="submit" id="subDados" class="submit-button dados" value="Atualizar dados">  
        </form>
    </div>  
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

<div id="modalContato" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Contato</h2>
        <div id="alerta-contato"></div>
        <form id="formcontato" action="php/contato.php">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_contato'] ?? '' ?>">
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="nomeContato" name="nome" placeholder="Nome completo" value="<?= $nome ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="emailContato" name="email" placeholder="Email" value="<?= $email ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-comment"></i>
                    <input type="text" id="assunto" name="assunto" placeholder="Assunto">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-pencil-alt"></i>
                    <textarea id="mensagem" name="mensagem" rows="4" style="height: 60px; resize: none;" placeholder="Mensagem"></textarea>
                </div>
            </div>
            <input type="submit" id="subContato" class="submit-button contato" value="Enviar">
        </form>
    </div>
</div>

<div id="modalRaspadinha" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Raspadinha Premiada</h2>
        <div id="alerta-raspadinha"></div>
            <p class="RaspadinhafomP">Ganhe <strong>bônus</strong> ao achar 3 iguais</p>
        <div id="raspadinha-container">
            <div id="raspadinha-numeros"></div>
            <canvas id="raspadinha-canvas"></canvas>
        </div>
        <div id="raspadinha-resultado"></div>
        <form id="formraspadinha" action="php/raspadinha.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_raspadinha'] ?? '' ?>">
            <input type="hidden" name="bonus" id="valor-bonus">
            <input type="submit" id="subRaspadinha" class="submit-button" value="Resgatar Bônus" style="display: none;">
        </form>
    </div>
</div>

<!-- Conteúdo dos modais FIM -->

<!-- Container topo -->      
<div class="top-bar">
    <div class="container">
        <div class="logo">
            <i class="fas fa-bars menu-icon" onclick="openMenu()"></i> <!-- Ícone de 3 tracinhos -->
            <img src="../imagens/<?= $Logo ?>">
        </div>
        <div class="buttons">
            <button class="button modalDeposito"><i class="fas fa-wallet fa-2x"></i><br>Depositar</button>

            <!-- Novo bloco para saldo e botão retirar -->
            <div class="saldo-retirar">
                <div class="saldo-titulo">Saldo</div>
                <div class="saldo">R$ <?php echo number_format($saldo, 2, ',', ''); ?></div>
                <button class="button btnRetirar modalRetirada">Retirar</button>
            </div>

            <!-- Novo bloco para bônus -->
            <div class="bonus-resgatar">
                <div class="bonus-titulo">Bônus</div>
                <div class="bonus-valor">R$ <?php echo number_format($saldo_bonus ?? 0, 2, ',', ''); ?></div>
                <button class="button btnResgatar modalBonus">Resgatar</button>
            </div>

        </div>
    </div>
</div>

<!-- Botão Esporte --> 
<?php if ($bet_esporte === 1): ?>
    <div class="botao-esporte">
        <?php include 'funcoes/esportes.php'; ?>
    </div>
<?php endif; ?>

<!-- Container slider -->  
<div class="slider-wrapper">
  <div class="slider-container">
    <div class="slides" id="slides">
      <div class="slide"><img src="../imagens/<?= $slider1 ?>"></div>
      <div class="slide"><img src="../imagens/<?= $slider2 ?>"></div>
      <div class="slide"><img src="../imagens/<?= $slider3 ?>"></div>
    </div>
  </div>

  <div class="dots" id="dots">
    <span class="dot active" data-index="0"></span>
    <span class="dot" data-index="1"></span>
    <span class="dot" data-index="2"></span>
  </div>
</div>

<!-- carrossel-ganhadores -->
<div class="ganhos-container">
  <div class="ganhos-fixo">
    <i class="fas fa-trophy"></i>
    <span>MAIORES<br>GANHOS<br>DE HOJE</span>
  </div>
  <div class="ganhos-rolando">
    <div class="ganhos-slider" id="ganhos-slider">
      <?php include 'funcoes/carrossel-ganhadores.php'; ?>
    </div>
  </div>
</div>

<!-- Container busca --> 
    <div class="busca-container">
    <form id="form-busca" onsubmit="return false;">
  <div class="busca-input-icon">
      <i class="fas fa-search"></i>
      <input type="text" id="input-busca" name="busca" placeholder="Buscar jogo..." class="busca-input" autocomplete="off">
  </div>
</form>
    <div id="resultado-busca" class="busca-resultado"></div>
</div>

<div class="content-box">
    <?php 
            $pagina = $_GET["pagina"] ?? null;

    switch ($pagina) {
        case 'extrato':
            include "extrato.php";
            break;

        case 'afiliado':
            include "afiliado.php";
            break;

        default:
            include "dashboard.php";
            break;
    }
           ?>
</div>

<!-- Resultados ao Vivo -->
<div class="aovivo-resultados">
  <span class="aovivo-titulo">
  <span class="online-dot-red"></span> Resultados ao Vivo
</span>
  <div class="aovivo-resultado-container" id="aovivo-container"></div>
  <?php include 'funcoes/aovivo.php'; ?>
</div>

<!-- Modal Único -->
<div id="modal" class="modal-slots">
    <div class="modal-slots-content">
        <button class="close-slots-modal" onclick="closeGameModal()"><i class="fas fa-times"></i></button>
        <iframe id="iframe" width="100%" height="100%" frameborder="0"></iframe>
    </div>
</div>

<!-- Container footer -->
 <div class="footer">
    <div class="container-footer">
        <div class="footer-column">
            <img src="../imagens/<?= $Logo ?>">
        </div>

        <div class="footer-column">
            <h4>Sobre Nós</h4>
            <ul>
                <li><a id="termo-condicao">Termos e condições</a></li>
                <li><a id="termo-privacidade">Privacidade</a></li>
                <li><a id="termo-cookies">Política de cookies</a></li>
                <li><a id="termo-18anos">18 anos ou mais</a></li>
                <li><a id="termo-jogo-responsavel">Jogo Responsável</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Contato</h4>
            <ul> 
                <li><a class="modalContato">Fale Conosco</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Pagamento</h4>
            <img class="pix-logo" src="../imagens/logopix.png">
        </div>
        <div class="footer-column">
    <h4>Siga-nos</h4>
    <div class="social-icons">
        <a href="<?= $Instagram ?>" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="<?= $Telegram ?>" target="_blank"><i class="fab fa-telegram-plane"></i></a>
    </div>
</div>
    </div>

    <div class="footer-line"></div>

    <div class="footer-text" id="footerText">
        Jogue com responsabilidade, Apostar pode ser viciante! O jogo pode ser prejudicial se não for controlado e feito com responsabilidade. Por isso, leia todas as informações disponíveis na nossa seção de Jogo Responsável. O acesso de pessoas menores de 18 anos é estritamente proibido.<br><br>
        <div id="extraContent" style="display: none;">
        Nossa Plataforma não promove suas atividades para menores de 18 anos. Crianças devem sempre ter permissão dos pais antes de enviar qualquer informação pessoal (como nome, endereço de e-mail e número de telefone) pela internet, seja para qualquer pessoa ou para nós. Se você tem menos de 18 anos (ou idade inferior àquela legalmente permitida para fazer apostas em seu país de residência), solicitamos que não acesse nossa plataforma. Jogar pode ser algo viciante. Jogue com responsabilidade. Para mais informações, visite nossa página Jogos Responsáveis.<br><br>
        Ao navegar nesse site aceite o uso de certos cookies de navegador com o objetivo de melhorar a sua experiência. Nossa plataforma apenas usa cookies que melhoram a sua experiência e não interferem em sua privacidade. Por favor acesse à nossa Política de Privacidade para mais informação em relação à forma como utilizamos cookies e como pode desativar ou gerenciar os mesmos, caso queira.
        </div>
        <button id="toggleButton" class="ver-mais-btn">Ver mais</button>
    </div>

    <div class="footer-line"></div>

    <div class="footer-centered-img">
        <img class="selo-img" src="../imagens/logosigap.png">
    </div>

    <div class="footer-line"></div>

    <div class="footer-bottom">
        <p>
            <strong>Suporte</strong> suporte@<?= $Site ?>
            <span>|</span>
            <strong>Jurídico</strong> juridico@<?= $Site ?>
            <span>|</span>
            <strong>Parceiros</strong> parceiros@<?= $Site ?>
        </p>
        <p>© 2025 <?= $NomeSite ?>. Todos os direitos reservados.</p>
    </div>
</div>  
    
</body>
</html>