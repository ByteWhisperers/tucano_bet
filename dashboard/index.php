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
    $token_resgatar_bau = gerar_token_csrf('resgatar_bau');
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
    $stmt = $pdo->prepare("SELECT bet_nome, bet_email, bet_cpf, bet_saldo, bet_afiliado_por, bet_celular FROM bet_usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nome  = $usuario['bet_nome'];
        $email = $usuario['bet_email'];
        $cpf   = $usuario['bet_cpf'];
        $saldo = $usuario['bet_saldo'];
        $porcentagem = $usuario['bet_afiliado_por'];
        $celular = $usuario['bet_celular'];
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
    <link rel="stylesheet" href="../css/tropical_core.css">
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
    <a href="/dashboard/?pagina=perfil&aba=historico"><i class="fas fa-file-invoice-dollar"></i> Extrato</a>
    <a href="/dashboard/?pagina=perfil"><i class="fas fa-user"></i> Perfil</a>
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

<input type="hidden" name="csrf_token_resgatar_bau" value="<?= $token_resgatar_bau ?>">

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
                <div class="saldo" data-saldo>R$ <?php echo number_format($saldo, 2, ',', ''); ?></div>
                <button class="button btnRetirar modalRetirada">Retirar</button>
            </div>

            <!-- Novo bloco para bônus -->
            <div class="bonus-resgatar">
                <div class="bonus-titulo">Bônus</div>
                <div class="bonus">R$ <?php echo number_format($saldo_bonus, 2, ',', ''); ?></div>
                <button class="button btnBonus modalBonus">Resgatar</button>
            </div>
        </div>
    </div>
</div>
<!-- Container topo FIM -->

<!-- Conteúdo Principal -->
<div class="main-content">
    <?php
    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'dashboard';
    
    switch ($pagina) {
        case 'dashboard':
            include 'dashboard.php';
            break;
        case 'extrato':
            // Redirecionar extrato para perfil com aba historico
            include 'perfil.php';
            break;
        case 'afiliado':
            include 'afiliado.php';
            break;
        case 'perfil':
            include 'perfil.php';
            break;
        default:
            include 'dashboard.php';
            break;
    }
    ?>
</div>

<!-- Rodapé -->
<footer class="footer">
    <div class="container">
        <div class="footer-links">
            <a href="#" id="termo-condicao">Termos e Condições</a>
            <a href="#" id="termo-privacidade">Política de Privacidade</a>
            <a href="#" id="termo-cookies">Política de Cookies</a>
            <a href="#" id="termo-18anos">Aviso 18+</a>
            <a href="#" id="termo-jogo-responsavel">Jogo Responsável</a>
            <a class="modalContato">Suporte</a>
        </div>
        <div class="footer-social">
            <?php if (!empty($Instagram)): ?><a href="<?= $Instagram ?>" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
            <?php if (!empty($Telegram)): ?><a href="<?= $Telegram ?>" target="_blank"><i class="fab fa-telegram"></i></a><?php endif; ?>
        </div>
        <p>&copy; <?= date('Y') ?> <?= $NomeSite ?>. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>
