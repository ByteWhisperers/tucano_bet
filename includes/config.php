<?php
try {
     // Buscar valores no banco de dados
    $stmt = $pdo->prepare("SELECT bet_gerapix, bet_playfiver_publico, bet_playfiver_secreto, bet_valor_deposito, bet_valor_retirada, bet_face_pixel, bet_face_token, bet_email_host, bet_email_email, bet_email_senha, bet_email_porta, bet_email_smtp, bet_logo, bet_favicon, bet_slider, bet_site_nome, bet_site_url, bet_cor, bet_instagram, bet_telegram, bet_pag_tipo, bet_bonus_cadastro, bet_bonus_raspadinha, bet_stop_games, bet_esporte, bet_campocel FROM bet_adm_config WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    $Logo = !empty($config['bet_logo']) ? $config['bet_logo'] : 'logo.png';
    $Favicon = !empty($config['bet_favicon']) ? $config['bet_favicon'] : 'favicon.png';
    $NomeSite = !empty($config['bet_site_nome']) ? $config['bet_site_nome'] :'GeraBet';
    $Site = !empty($config['bet_site_url']) ? str_replace(['http://', 'https://'], '', $config['bet_site_url']) : 'gerabet.bet';
    $Instagram = !empty($config['bet_instagram']) ? $config['bet_instagram'] : 'https://www.instagram.com/';
    $Telegram  = !empty($config['bet_telegram'])  ? $config['bet_telegram']  : 'https://web.telegram.org/';

    $FacePixel = !empty($config['bet_face_pixel'])  ? $config['bet_face_pixel']  : '464894353309714';
    $FaceToken = !empty($config['bet_face_token'])  ? $config['bet_face_token']  : '';
   
    $EmailHost  = !empty($config['bet_email_host'])  ? $config['bet_email_host']  : '';
    $EmailEmail = !empty($config['bet_email_email']) ? $config['bet_email_email'] : '';
    $EmailSenha = !empty($config['bet_email_senha']) ? $config['bet_email_senha'] : '';
    $EmailPorta = !empty($config['bet_email_porta']) ? $config['bet_email_porta'] : '';
    $EmailSMTP  = !empty($config['bet_email_smtp'])  ? $config['bet_email_smtp']  : '';

    $ValorDeposito = !empty($config['bet_valor_deposito']) ? $config['bet_valor_deposito'] : 10;
    $ValorRetirada = !empty($config['bet_valor_retirada']) ? $config['bet_valor_retirada'] : 10;

    $TipoPagamento = isset($config['bet_pag_tipo']) ? (int)$config['bet_pag_tipo'] : 0;

    $TokenGeraPix = !empty($config['bet_gerapix'])  ? $config['bet_gerapix']  : '';
    // Split GeraPix
    $LoginSplit = '';
    $PorcentagemSplit = '';

    $TokenPlayFiverPublico = !empty($config['bet_playfiver_publico'])  ? $config['bet_playfiver_publico']  : '';
    $TokenPlayFiverSecreto = !empty($config['bet_playfiver_secreto'])  ? $config['bet_playfiver_secreto']  : '';

    $ValorBonusCadastro = !empty($config['bet_bonus_cadastro']) ? $config['bet_bonus_cadastro'] : 0;
    $ChaveBonusRaspadinha = isset($config['bet_bonus_raspadinha']) ? (int)$config['bet_bonus_raspadinha'] : 0;

    $bet_stop_games = isset($config['bet_stop_games']) ? (int)$config['bet_stop_games'] : 0;
    $bet_esporte = isset($config['bet_esporte']) ? (int)$config['bet_esporte'] : 0;
    $bet_campocel = isset($config['bet_campocel']) ? (int)$config['bet_campocel'] : 0;

    // Cor personalizada
    $color = !empty($config['bet_cor']) ? $config['bet_cor'] : '#00C774_#00B066_#000000';
    list($corPrincipal, $corHover, $corTexto) = explode('_', $color);


    // Slider: divide em três imagens, ou usa padrão se vazio
    if (!empty($config['bet_slider'])) {
        $sliders = explode(',', $config['bet_slider']);
        $slider1 = !empty($sliders[0]) ? $sliders[0] : 'slider1.png';
        $slider2 = !empty($sliders[1]) ? $sliders[1] : 'slider2.png';
        $slider3 = !empty($sliders[2]) ? $sliders[2] : 'slider3.png';
    } else {
        $slider1 = 'slider1.png';
        $slider2 = 'slider2.png';
        $slider3 = 'slider3.png';
    }

} catch (Exception $e) {
    // Valores padrão em caso de erro
    $Logo = 'logo.png';
    $Favicon = 'favicon.png';
    $slider1 = 'slider1.png';
    $slider2 = 'slider2.png';
    $slider3 = 'slider3.png';
    $NomeSite = 'GeraBet';
    $Site = 'gerabet.bet';
    $corPrincipal = '#00C774';
    $corHover = '#00B066';
    $corTexto = '#000000';
    $Instagram = 'https://www.instagram.com/';
    $Telegram = 'https://web.telegram.org/';
}
?>