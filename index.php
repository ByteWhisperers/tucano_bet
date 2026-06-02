<?php
session_start();

// Gerar um token CSRF único para cada página
function gerar_token_csrf($form) {
    $token = bin2hex(random_bytes(32));
    $_SESSION["csrf_token_$form"] = $token;
    return $token;
}

// Exemplo de como gerar para diferentes formulários
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Gerando o token para cada formulário
    $token_contato = gerar_token_csrf('contato');
    $token_login = gerar_token_csrf('login');
    $token_cadastro = gerar_token_csrf('cadastro');
    $token_recuperar_senha = gerar_token_csrf('recuperar_senha');
}

define('IN_INDEX', true);
require_once 'includes/db.php';
require_once 'includes/config.php';

// Se já estiver logado via sessão
if (isset($_SESSION['usuario_id'])) {
    header("Location: /dashboard/");
    exit;
}

// Se tiver o token no cookie, tenta autenticar
if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];

    // Consulta com prefixo bet_ nas colunas e tabela
    $stmt = $pdo->prepare("SELECT id, bet_status FROM bet_usuarios WHERE bet_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['bet_status'] == 1) {
        $_SESSION['usuario_id'] = $usuario['id'];
        header("Location: /dashboard/");
        exit;
    } else {
        // Apaga o cookie se o token for inválido
        setcookie("auth_token", "", time() - 3600, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title><?= $NomeSite ?> Cassino e Apostas Online com Bônus Exclusivos</title>
    <link rel="icon" type="image/png" href="imagens/<?= $Favicon ?>">

        <!-- Meta Tags Essenciais -->
        <meta name="description" content="Jogue na <?= $NomeSite ?> e descubra o melhor cassino online ao vivo! Aposte em esportes e cassino com bônus exclusivos. Depósitos rápidos via Pix.">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= $NomeSite ?> - O Melhor Cassino Online e Apostas Esportivas">
        <meta property="og:description" content="Aposte e ganhe na <?= $NomeSite ?>! Cassino ao vivo, jogos de roleta, blackjack, slots e apostas esportivas. Depósitos via Pix e saques instantâneos.">
        <meta property="og:image" content="https://<?= $Site ?>/imagens/<?= $Logo ?>">
        <meta property="og:url" content="https://<?= $Site ?>">
        <meta property="og:site_name" content="<?= $NomeSite ?>">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= $NomeSite ?> - Cassino Online e Apostas com Pix">
        <meta name="twitter:description" content="Entre no mundo das apostas com <?= $NomeSite ?>! Cassino ao vivo, jogos exclusivos e bônus especiais para novos jogadores.">
        <meta name="twitter:image" content="https://<?= $Site ?>/imagens/<?= $Logo ?>">

        <!-- SEO Keywords -->
        <meta name="keywords" content="Cassino online, apostas esportivas, jogos de azar, slots, roleta, blackjack, poker, cassino ao vivo, bônus cassino, <?= $NomeSite ?>, apostas com Pix">

    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo $FacePixel; ?>'); // aqui a variável PHP é inserida
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo $FacePixel; ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/tropical_core.css">
    <link rel="stylesheet" href="css/estilos.php">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>

   <!-- Conteúdo dos termos -->
<div id="sidebar" class="sidebar">
    <span class="close-sidebar"><i class="fas fa-times"></i></span> 
    <div class="sidebar-content" id="sidebarContent"></div>
</div>    
 

<!-- Conteúdo dos modais -->
<div class="overlay" id="overlay"></div>

<div id="modalCadastro" class="modal">
    <div  class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Criar conta</h2>
        <div id="alerta-cadastro"></div>
        <form id="formcadastro" action="php/cadastro.php">

            <?php
                $ref = (isset($_GET['ref']) && is_numeric($_GET['ref'])) ? $_GET['ref'] : '';
                $utm = (isset($_GET['utm']) && !empty($_GET['utm'])) ? $_GET['utm'] : '';
            ?>
            <input type="hidden" name="ref" value="<?php echo htmlspecialchars($ref); ?>">
            <input type="hidden" name="utm" value="<?php echo htmlspecialchars($utm, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_cadastro'] ?? '' ?>">

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="nome" name="nome" placeholder="Nome completo">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="email" name="email" placeholder="Email">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="cpf" name="cpf" placeholder="CPF">
                </div>
            </div>

            <?php if ($bet_campocel === 1): ?>
            <div class="form-row">
                    <div class="input-icon">
                    <i class="fas fa-mobile-alt"></i>
                    <input type="text" id="celular" name="celular" placeholder="Celular">
                </div>
            </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-calendar"></i>
                    <input type="text" id="nascimento" name="nascimento" placeholder="Data de nascimento">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senha" name="senha" placeholder="Senha com no mínimo 8 caracteres">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirmasenha" name="confirmasenha" placeholder="Confirme a senha">
                </div>
            </div>

            <input type="submit" id="subCadastro" class="submit-button abrir-conta" value="Abrir Conta">

            <div class="termos">
             <p>Ao abrir uma conta:</p>
             Eu aceito os <a id="termo-condicao">Termos e condições</a> da <?= $NomeSite ?>. Li e entendi a <a id="termo-privacidade">Política de privacidade</a> e <a id="termo-cookies">Política de cookies</a> da <?= $NomeSite ?>, conforme publicado no site e confirmo que tenho <a id="termo-18anos">18 anos de idade ou mais</a>
            </div>
            
        </form> 
    </div>
</div>  

<div id="modalLogin" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Login</h2>
        <div id="alerta-login"></div>
        <form id="formlogin" action="php/login.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_login'] ?? '' ?>"> 
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="cpflogin" name="cpf" placeholder="CPF">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senhaLogin" name="senha" placeholder="Senha">
                </div>
            </div>

            <div class="recover-password">
                <a class="modalRecuperarSenha">Recuperar Senha</a>
            </div>
            <input type="submit" id="subLogin" class="submit-button" value="Entrar">
        </form>
            <div class="create-account modalCadastro"><a>Criar Conta</a></div> 
    </div>
</div>  

<div id="modalRecuperarSenha" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Recuperar Senha</h2>
        <div id="alerta-senha"></div>
        <form id="formsenha" action="php/recuperar.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_recuperar_senha'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="cpfrecuperar" name="cpf" placeholder="CPF">
                </div>
            </div>
            <input type="submit" id="subSenha" class="submit-button recuperar" value="Recuperar">
        </form>
            <div class="log-in modalLogin"><a>Efetuar o Login</a></div> 
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
                    <input type="text" id="nomeContato" name="nome" placeholder="Nome completo">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" id="emailContato" name="email" placeholder="Email">
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
<!-- Conteúdo dos modais FIM -->

 <!-- Container Bônus --> 
<?php if ($ValorBonusCadastro > 0): ?>
  <div class="bonus-cadastro">
    🔥 CADASTRE-SE E GANHE R$ <?= number_format($ValorBonusCadastro, 2, ',', '.') ?> REAIS DE BÔNUS
  </div>
<?php endif; ?>

 <!-- Container topo -->      
    <div class="top-bar">
        <div class="container">
            <div class="logo">
                <img src="imagens/<?= $Logo ?>">
            </div>
            <div class="buttons">
                <button class="button  modalCadastro">Criar Conta</button>
                <button class="button  modalLogin">Login</button>
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
      <div class="slide"><img src="imagens/<?= $slider1 ?>"></div>
      <div class="slide"><img src="imagens/<?= $slider2 ?>"></div>
      <div class="slide"><img src="imagens/<?= $slider3 ?>"></div>
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

<!-- Jogos da semana -->
<div class="lista-jogos">
    <span class="titulo-lista-jogos">🔥 Jogados Da Semana</span>
    <div class="jogos-container">
        <?php include 'funcoes/jogo-semana.php'; ?>
    </div>
</div>

<!-- Jogos da PRAGMATIC -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> PRAGMATIC</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-pragmatic.php'; ?>
    </div>
</div>

<!-- Jogos da PGSOFT -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> PGSOFT</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-pgsoft.php'; ?>
    </div>
</div>

<!-- Jogos da SPRIBE -->
<div class="lista-jogos">
   <span class="titulo-lista-jogos"> SPRIBE</span> 
   <div class="jogos-container">
        <?php include 'funcoes/jogo-spribe.php'; ?>
    </div>
</div>

<!-- Jogos todos -->
<div class="lista-jogos">
    <span class="titulo-lista-jogos"> Todos os jogos</span> 
  <div id="jogosCarregaveis" class="jogos-container"></div>
  <button id="verMaisBtn" class="btn-ver-mais">Ver mais</button>
</div>

<div class="footer-line"></div>

<!-- Resultados ao Vivo -->
<div class="aovivo-resultados">
  <span class="aovivo-titulo">
  <span class="online-dot-red"></span> Resultados ao Vivo
</span>
  <div class="aovivo-resultado-container" id="aovivo-container"></div>
  <?php include 'funcoes/aovivo.php'; ?>
</div>

 <!-- Container footer -->
 <div class="footer">
    <div class="container-footer">
        <div class="footer-column">
            <img src="imagens/<?= $Logo ?>">
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
            <img class="pix-logo" src="imagens/logopix.png">
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
        <img class="selo-img" src="imagens/logosigap.png">
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