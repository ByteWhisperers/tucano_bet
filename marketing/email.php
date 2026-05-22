<?php

// =========================
// INCLUDES
// =========================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../phpmailer/PHPMailerAutoload.php';

// =========================
// BUSCA 1 EMAIL PENDENTE
// =========================
$sql = "
SELECT 
    me.bet_id AS envio_id,
    u.bet_email AS email,
    u.bet_nome AS nome,
    mc.bet_assunto AS assunto,
    mc.bet_mensagem AS mensagem
FROM bet_marketing_envios me
JOIN bet_usuarios u 
    ON u.id = me.bet_usuario_id
JOIN bet_marketing_config mc 
    ON mc.bet_id = me.bet_config_id
WHERE me.bet_status = 'pendente'
  AND mc.bet_ativa = 1
ORDER BY me.bet_id ASC
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    exit("Nenhum e-mail pendente\n");
}

$envioId = $result['envio_id'];

// =========================
// CONFIGURA PHPMailer
// =========================
$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = $EmailHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $EmailEmail;
    $mail->Password   = $EmailSenha;
    $mail->SMTPSecure = $EmailSMTP; // ssl ou tls
    $mail->Port       = $EmailPorta;

    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);

    $mail->setFrom($EmailEmail, $NomeSite ?? $NomeSite);

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    // =========================
    // FORMATA NOME
    // =========================
    $nome = trim($result['nome']);
    $nome = ucwords(strtolower($nome));
    $partes = explode(' ', $nome);
    $nomeFormatado = $partes[0] . (count($partes) > 1 ? ' ' . end($partes) : '');

    // Variáveis disponíveis no template:
    // {{nome}}  → nome do usuário
    // {{email}} → email do usuário
    $mensagem = str_replace(
        ['{{nome}}', '{{email}}'],
        [$nomeFormatado, $result['email']],
        $result['mensagem']
    );

    // =========================
    // ENVIO
    // =========================
    $mail->addAddress($result['email']);
    $mail->Subject = $result['assunto'];
    $mail->Body    = $mensagem;

    $mail->send();

    // =========================
    // MARCA COMO ENVIADO
    // =========================
    $pdo->prepare("
        UPDATE bet_marketing_envios
        SET 
            bet_status = 'enviado',
            bet_data_envio = NOW()
        WHERE bet_id = ?
    ")->execute([$envioId]);

    echo "E-mail enviado\n";

} catch (Exception $e) {

    // =========================
    // MARCA COMO ERRO
    // =========================
    $pdo->prepare("
        UPDATE bet_marketing_envios
        SET 
            bet_status = 'erro',
            bet_tentativas = bet_tentativas + 1
        WHERE bet_id = ?
    ")->execute([$envioId]);

    http_response_code(500);
    echo "Erro no envio\n";
}
