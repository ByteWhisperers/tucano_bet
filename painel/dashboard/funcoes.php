<?php
if (!defined('IN_INDEX')) {
    header("Location: /painel/dashboard/");
    exit();
}
?>
<div class="container-conteudo">
       <h2 class="titulo-funcoes">Funções</h2>
        <div class="grid-boxes">
            <button class="box-funcao modalGeraPix">
                <i class="fas fa-money-bill-wave"></i>
                <span>GeraPix (Gateway)</span>
            </button>

            <button class="box-funcao modalPlayFiver">
                <i class="fas fa-gamepad"></i>
                <span>PlayFiver (Jogos)</span>
            </button>

             <button class="box-funcao modalValores">
                <i class="fas fa-coins"></i>
                <span>Depósito/Saque</span>
            </button>

            <button class="box-funcao modalFacebook">
                <i class="fab fa-facebook"></i>
                <span>Facebook (API)</span>
            </button>

            <button class="box-funcao modalEmail">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            </button>

            <button class="box-funcao modalLogo">
                <i class="fas fa-image"></i>
                <span>Logo</span>
            </button>

            <button class="box-funcao modalFavicon">
                <i class="fas fa-star"></i>
                <span>Favicon</span>
            </button>

            <button class="box-funcao modalSlider">
                <i class="fas fa-images"></i>
                <span>Slider</span>
            </button>

            <button class="box-funcao modalNomeUrl">
                <i class="fas fa-globe"></i>
                <span>Nome / URL</span>
            </button>

            <button class="box-funcao modalCores">
                <i class="fas fa-palette"></i>
                <span>Cores</span>
            </button>

            <button class="box-funcao modalRedes">
                <i class="fas fa-share-alt"></i>
                <span>Redes Sociais</span>
            </button>

            <button class="box-funcao modalBonusCadastro">
                <i class="fas fa-gift"></i>
                <span>Bônus Cadastro</span>
            </button>

            <button class="box-funcao modalBonusRaspadinha">
                <i class="fas fa-gift"></i>
                <span>Bônus Raspadinha</span>
            </button>

            <button class="box-funcao modalAfiliados">
                <i class="fas fa-users"></i>
                <span>Afiliados Porcentagem</span>
            </button>

            <button class="box-funcao modalStopGames"> 
                <i class="fas fa-exclamation-triangle"></i>
                <span>Status dos jogos</span> 
            </button>

            <button class="box-funcao modalEsporte"> 
                <i class="fas fa-futbol"></i>
                <span>Ativar Esportes</span> 
            </button>

            <button class="box-funcao modalCampoCel"> 
                <i class="fas fa-mobile-alt"></i>
                <span>Ativar Campo Cel</span> 
            </button>
        </div>
</div>

<!-- Conteúdo dos modais -->
<div id="modalGeraPix" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Credenciais GeraPix</h2>
        <div id="alerta-gerapix"></div>
        <form id="formgerapix" action="php/gerapix.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_gerapix'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="tokengerapix" placeholder="SEU TOKEN SECRETO"  value="<?= $TokenGeraPix ?>">
                </div>
            </div>

            <input type="submit" id="subGerapix" class="submit-button espacobutton" value="Atualizar">
        </form>
            <div class="create-account"><a href="https://gerapix.digital/" target="_blank" rel="noopener noreferrer">Abrir conta na GeraPix</a></div> 
    </div>
</div>

<div id="modalPlayFiver" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Credenciais PlayFiver</h2>
        <div id="alerta-playfiver"></div>
        <form id="formplayfiver" action="php/playfiver.php">
               <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_playfiver'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="tokenplayfiverpublico" placeholder="SEU TOKEN PÚBLICO" value="<?= $TokenPlayFiverPublico ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="tokenplayfiversecreto" placeholder="SEU TOKEN SECRETO" value="<?= $TokenPlayFiverSecreto ?>">
                </div>
            </div>
            <input type="submit" id="subPlayfiver" class="submit-button espacobutton" value="Atualizar">
        </form>
            <div class="link-modal">
                <p>Sua URL de webhook para cadastro na PlayFiver</p>
                <p id="link-text-1"><?= 'https://' . $_SERVER['HTTP_HOST'] . '/webhook/index.php'; ?></p>
                <button class="copy-btn" data-target="link-text-1">Copiar Link</button>
            </div>
            <div class="link-modal">

<?php
$testUrl = "https://api.playfivers.com/api/v2/game_launch";
$postData = ["ping" => "1"];

function testarConexao($url, $postData, $tipo)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Força IPv4 ou IPv6
    if ($tipo === 'ipv4') {
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    } elseif ($tipo === 'ipv6') {
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
    }

    curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return $info['local_ip'] ?? null;
}

// Testa IPv4 e IPv6
$ip4 = testarConexao($testUrl, $postData, 'ipv4');
$ip6 = testarConexao($testUrl, $postData, 'ipv6');

// Exibir resultado com fallback de mensagem
echo "<h3>Seu IP para cadastrar na PlayFiver</h3>";
echo "<p><strong>IPv4:</strong> " . htmlspecialchars($ip4 ?? 'Não encontrado') . "</p>";
echo "<p><strong>IPv6:</strong> " . htmlspecialchars($ip6 ?? 'Não encontrado') . "</p>";
?>

            </div>
            <div class="create-account"><a href="https://playfiver.app/register" target="_blank" rel="noopener noreferrer">Abrir conta na PlayFiver</a></div> 
    </div>
</div>

<div id="modalValores" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Depósito/Saque</h2>
        <div id="alerta-valores"></div>
        <form id="formvalores" action="php/valores.php">
               <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_valores'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-wallet"></i>
                    <input type="text" id='deposito' name="deposito" placeholder="Valor mínimo de depósito">
                </div>
            </div>
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                    <input type="text" id='retirada' name="retirada" placeholder="Valor mínimo de saque">
                </div>
            </div>

           <div class="form-row toggle-row">
    <label class="toggle-switch">
        <input type="checkbox" id="togglePagamento" name="togglePagamento">
        <span class="switch-slider"></span>
    </label>
    <span class="toggle-label">Pagamento Automático</span>
</div>

<input type="hidden" name="pagamento_auto" id="pagamento_auto" value="0">

            <input type="submit" id="subValores" class="submit-button espacobutton" value="Atualizar">
        </form>
        <div class="info-modal">
            <p>Valor atual de depósito R$ <strong><?= number_format($ValorDeposito, 2, ',', '.') ?></strong> reais.</p>
            <p>Valor atual de retirada R$ <strong><?= number_format($ValorRetirada, 2, ',', '.') ?></strong> reais.</p>
            <p>Pagamento tipo: <strong><?= ($TipoPagamento == 1) ? "Automático" : "Manual"; ?></strong></p>
        </div>
    </div>
</div>

<div id="modalFacebook" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Facebook (API)</h2>
        <div id="alerta-facebook"></div>
        <form id="formfacebook" action="php/facebook.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_facebook'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-code"></i>
                    <input type="text" name="facepixel" placeholder="Seu Pixel FaceBook" value="<?= $FacePixel ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="facetoken" placeholder="Seu Token FaceBook" value="<?= $FaceToken ?>">
                </div>
            </div>

            <input type="submit" id="subFacebook" class="submit-button espacobutton" value="Atualizar">
        </form>
<div class="link-modal">
    <p>Sua URL de divulgação no FaceBook</p>
    <p id="link-text-2"><?= 'https://' . $_SERVER['HTTP_HOST'] . '/?modal=cadastro&utm=facebook'; ?></p>
    <button class="copy-btn" data-target="link-text-2">Copiar Link</button>
</div>
    </div>
</div>

<div id="modalEmail" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Configurações de Email</h2>
        <div id="alerta-email"></div>
        <form id="formemail" action="php/email.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_email'] ?? '' ?>">
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-server"></i>
                    <input type="text" name="host" placeholder="Servidor de Saída (SMTP)">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" name="email" placeholder="E-mail de Suporte do Site">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="senha" placeholder="Senha da Conta de Email">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-plug"></i>
                    <select name="porta">
                        <option value="">Selecione a Porta SMTP</option>
                        <option value="465">465 (SMTP com SSL/TLS)</option>
                        <option value="587">587 (SMTP com STARTTLS)</option>
                        <option value="25">25 (SMTP padrão, sem criptografia ou com STARTTLS)</option>
                     </select>
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-shield-alt"></i>
                    <select name="smtp">
                        <option value="">Tipo de Segurança</option>
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                        </select>
                    </div>
                </div>

            <input type="submit" id="subEmail" class="submit-button espacobutton" value="Atualizar">
        </form>
<div class="link-modal">
    <p>Sua URL para cadastro no site cron-job.org</p>
    <p id="link-text-3"><?= 'https://' . $_SERVER['HTTP_HOST'] . '/marketing/email.php'; ?></p>
    <button class="copy-btn" data-target="link-text-3">Copiar Link</button>
</div>
    </div>
</div>

<div id="modalLogo" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Imagem Logo</h2>
        <div id="alerta-logo"></div>
        <form id="formlogo" action="php/logo.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_logo'] ?? '' ?>">
           <div class="input-file-wrapper">
                <i class="fas fa-image icon-left"></i>
                <label class="fake-file-input" for="fileInputLogo">
                    <span id="file-name-logo">Imagem JPG - JPEG - PNG</span>
                    <i class="fas fa-upload icon-right"></i>
                </label>
                <input type="file" id="fileInputLogo" name="logo" accept="image/png, image/jpeg" />
            </div>

        <div class="progress-container" style="display: none;">
            <div class="progress-bar"></div>
        </div>

            <input type="submit" id="subLogo" class="submit-button espacobutton" value="Atualizar">
        </form>       
    </div>
</div>

<div id="modalFavicon" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Imagem Favicon</h2>
        <div id="alerta-favicon"></div>
        <form id="formfavicon" action="php/favicon.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_favicon'] ?? '' ?>">
           <div class="input-file-wrapper">
                <i class="fas fa-star icon-left"></i>
                <label class="fake-file-input" for="fileInputFavicon">
                    <span id="file-name-favicon">Favicon - 48x48 - PNG</span>
                    <i class="fas fa-upload icon-right"></i>
                </label>
                <input type="file" id="fileInputFavicon" name="favicon" accept="image/png" />
            </div>

        <div class="progress-container" style="display: none;">
            <div class="progress-bar"></div>
        </div>

            <input type="submit" id="subFavicon" class="submit-button espacobutton" value="Atualizar">
        </form>       
    </div>
</div>

<div id="modalSlider" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Imagens Sliders</h2>
        <div id="alerta-slider"></div>
        <form id="formslider" action="php/slider.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_slider'] ?? '' ?>">
           <div class="input-file-wrapper">
                <i class="fas fa-images icon-left"></i>
                <label class="fake-file-input" for="fileInputSlider">
                    <span id="file-name-slider">Slider 3 Imagens de 1000px x 200px</span>
                    <i class="fas fa-upload icon-right"></i>
                </label>
                <input type="file" id="fileInputSlider" name="slider[]" accept="image/png, image/jpeg" multiple />
            </div>

        <div class="progress-container" style="display: none;">
            <div class="progress-bar"></div>
        </div>

            <input type="submit" id="subSlider" class="submit-button espacobutton" value="Atualizar">
        </form>       
    </div>
</div>

<div id="modalNomeUrl" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Nome / URL</h2>
        <div id="alerta-nomeurl"></div>
        <form id="formnomeurl" action="php/nomeurl.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_nomeurl'] ?? '' ?>">
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-globe"></i>
                    <input type="text" name="nomesite" placeholder="Nome do Site" value="<?= $NomeSite ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-globe-americas"></i>
                    <input type="text" name="urlsite" placeholder="URL do site (https://)*" value="https://<?= $Site ?>">
                </div>
            </div>
            <input type="submit" id="subNomeUrl" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalCores" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Cor do Template</h2>
        <div id="alerta-cores"></div>
        <form id="formcores" action="php/cores.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_cores'] ?? '' ?>">

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#00C774_#00B066_#000000">
                <span class="custom-radio" style="background-color: #00C774;"></span>
                <span class="color-label">Verde Neon</span>
            </label>

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#FF8C42_#E67834_#000000">
                <span class="custom-radio" style="background-color: #FF8C42;"></span>
                <span class="color-label">Laranja</span>
            </label>

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#007C91_#00667A_#FFFFFF">
                <span class="custom-radio" style="background-color: #007C91;"></span>
                <span class="color-label">Azul Petróleo</span>
            </label>

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#FF2DC6_#E025B1_#FFFFFF">
                <span class="custom-radio" style="background-color: #FF2DC6;"></span>
                <span class="color-label">Pink Fluor</span>
            </label>

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#FFD700_#E6C200_#000000">
                <span class="custom-radio" style="background-color: #FFD700;"></span>
                <span class="color-label">Amarelo Neon</span>
            </label>

            <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#9D00FF_#BF40FF_#FFFFFF">
                <span class="custom-radio" style="background-color: #9D00FF;"></span>
                <span class="color-label">Roxo Neon</span>
            </label>

             <label class="color-option">
                <i class="fas fa-palette"></i>
                <input type="radio" name="cor" value="#D72638_#A91E2C_#FFFFFF">
                <span class="custom-radio" style="background-color: #D72638;"></span>
                <span class="color-label">Vermelho Suave</span>
            </label>

            <input type="submit" id="subCores" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalRedes" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Redes Sociais</h2>
        <div id="alerta-redes"></div>
        <form id="formredes" action="php/redes.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_redes'] ?? '' ?>">
            
            <div class="form-row">
                <div class="input-icon">
                    <i class="fab fa-instagram"></i>
                    <input type="text" name="instagram" placeholder="Link do Instagram" value="<?= $Instagram ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="input-icon">
                    <i class="fab fa-telegram-plane"></i>
                    <input type="text" name="telegram" placeholder="Link do Telegram" value="<?= $Telegram ?>">
                </div>
            </div>

            <input type="submit" id="subRedes" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalBonusCadastro" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Valor do Bônus Cadastro</h2>
        <div id="alerta-bonuscadastro"></div>
        <form id="formbonuscadastro" action="php/bonuscadastro.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_bonuscadastro'] ?? '' ?>">
                
                <div class="form-row">
                <div class="input-icon">
                    <i class="fas fa-wallet"></i>
                    <input type="text" id='bonuscadastro' name="valorbonus" placeholder="Valor de bônus">
                </div>
                </div>

                <div class="info-modal">
                    <p>Valor atual de Bônus R$ <strong><?= number_format($ValorBonusCadastro, 2, ',', '.') ?></strong> reais.</p>
                </div>

              <input type="submit" id="subBonusCadastro" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalBonusRaspadinha" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Ativar Bônus Raspadinha</h2>
        <div id="alerta-bonusraspadinha"></div>
        <form id="formbonusraspadinha" action="php/bonusraspadinha.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_bonusraspadinha'] ?? '' ?>">
                
            <div class="form-row"> 
                    <div class="input-icon">
                    <i class="fas fa-ticket-alt"></i>
                <select name="statusbonus">
                    <option value="">Selecione o status do bônus</option>
                    <option value="1">Ativar Bônus</option>
                    <option value="0">Desativar Bônus</option>
                </select>
            </div>
            </div>

                <div class="info-modal">
                  <p>Status atual de Bônus: <strong><?= (isset($ChaveBonusRaspadinha) && $ChaveBonusRaspadinha == 1) ? "Ativo" : "Desativado"; ?></strong></p>

                </div>

              <input type="submit" id="subBonusRaspadinha" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalAfiliados" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Afiliados Porcentagem</h2>
        <div id="alerta-afiliados"></div>
        <form id="formafiliados" action="php/afiliados.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_afiliados'] ?? '' ?>">
                
<div class="form-row"> 
  <div class="input-icon">
    <i class="fas fa-percentage"></i>
    <select name="porcentagem_afiliados" id="select-porcentagem">
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
              <input type="submit" id="subAfiliados" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>


<div id="modalStopGames" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Status dos jogos</h2>
        <div id="alerta-stopgames"></div>
        <form id="formstopgames" action="php/stopgames.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_stopgames'] ?? '' ?>">
                
            <div class="form-row"> 
                    <div class="input-icon">
                    <i class="fas fa-ticket-alt"></i>
                <select name="stopgames">
                    <option value="">Selecione o status dos jogos</option>
                    <option value="1">Em Manutenção</option>
                    <option value="0">Em Produção</option>
                </select>
            </div>
            </div>

                <div class="info-modal">
                  <p>Status atual dos jogos: <strong><?= (isset($bet_stop_games) && $bet_stop_games == 1) ? "Em Manutenção" : "Em Produção"; ?></strong></p>

                </div>

              <input type="submit" id="subStopGames" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalEsporte" class="modal">
    <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Ativar Esportes</h2>
        <div id="alerta-esportes"></div>
        <form id="formesportes" action="php/esportes.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_esportes'] ?? '' ?>">
                
            <div class="form-row"> 
                    <div class="input-icon">
                    <i class="fas fa-futbol"></i>
                <select name="esportes">
                    <option value="">Selecione o status dos esportes</option>
                    <option value="1">Ativar Esportes</option>
                    <option value="0">Desativar Esportes</option>
                </select>
            </div>
            </div>

                <div class="info-modal">
                  <p>Status atual dos esportes: <strong><?= (isset($bet_esporte) && $bet_esporte == 1) ? "Ativo" : "Desativado"; ?></strong></p>
                  <br>
                  <p>⚠️ Atenção: Ao ativar Esportes, é necessário possuir saldo em sua carteira ESPORTE da Playfiver. Caso contrário, ocorrerá um erro na API.</p>

                </div>

              <input type="submit" id="subEsporte" class="submit-button espacobutton" value="Atualizar">
        </form>
    </div>
</div>

<div id="modalCampoCel" class="modal"> 
     <div class="modal-content">
        <span class="close-modal"><i class="fas fa-times"></i></span>
        <h2>Ativar Campo Cel</h2>
        <div id="alerta-campocel"></div>

        <form id="formcampocel" action="php/campocel.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_campocel'] ?? '' ?>">

            <div class="form-row"> 
                <div class="input-icon">
                    <i class="fas fa-mobile-alt"></i>
                        <select name="celular">
                            <option value="">Selecione o status do campo</option>
                            <option value="1">Ativar campo celular</option>
                            <option value="0">Desativar campo celular</option>
                        </select>
                </div>
            </div>

            <div class="info-modal">
                  <p>Status atual do campo: <strong><?= (isset($bet_campocel) && $bet_campocel == 1) ? "Ativo" : "Desativado"; ?></strong></p><br>
            </div>

               <input type="submit" id="subCampoCel" class="submit-button espacobutton" value="Atualizar">
        </form>
         
     </div>
</div>