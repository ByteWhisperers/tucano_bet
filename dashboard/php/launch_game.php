<?php
session_start();

require_once '../../includes/db.php';
require_once '../../includes/config.php';
require_once 'auth.php';

// Verifica se o sistema está em manutenção
if (isset($bet_stop_games) && $bet_stop_games == 1) {
    die(json_encode([
        'status' => 'error',
        'msg' => 'Estamos em manutenção. Voltamos em 10 minutos!'
    ]));
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$saldo = $usuario['bet_saldo'] ?? 0;
$game_code = $_POST['game_id'] ?? null;

if (!$game_code) {
    die(json_encode(['status' => 'error', 'msg' => 'Código do jogo não informado']));
}

$sql = "SELECT game_code, game_original FROM bet_jogos WHERE game_code = :game_code LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->execute();

$jogo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jogo) {
    die(json_encode(['status' => 'error', 'msg' => 'Jogo não encontrado']));
}

$codigo = $jogo['game_code'];
$original = ($jogo['game_original'] == 1) ? true : false;

$apiUrl = 'https://api.playfivers.com/api/v2/game_launch';

$postData = [
    "agentToken"   => $TokenPlayFiverPublico,
    "secretKey"    => $TokenPlayFiverSecreto,
    "user_code"    => $usuario_id ?: 'UserOFF',
    "game_code"    => $codigo,
    "game_original"=> $original,
    "user_balance" => $saldo,
    "lang"         => "pt"
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

// 🔒 Forçar uso de IPv4
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

$response = curl_exec($ch);

if ($response === false) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    die(json_encode(['status' => 'error', 'msg' => 'Erro ao comunicar com a API']));
}

curl_close($ch);

$response_data = json_decode($response, true);

if ($response_data['status'] == 1) {
    echo json_encode([
        'status' => 'success',
        'launch_url' => $response_data['launch_url']
    ]);
} else {
    die(json_encode(['status' => 'error', 'msg' => 'Erro na resposta da API']));
}
