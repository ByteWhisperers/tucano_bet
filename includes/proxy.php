<?php

$url = $_GET['url'] ?? null;

// Se não passar nada, erro
if (!$url) {
    http_response_code(400);
    exit('URL não informada');
}

$permitido = "https://imagensfivers.com/Games/";
if (strpos($url, $permitido) !== 0) {
    http_response_code(403);
    exit('URL não permitida');
}

$ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

// Define o Content-Type
switch (strtolower($ext)) {
    case "jpg":
    case "jpeg":
        header("Content-Type: image/jpeg");
        break;
    case "png":
        header("Content-Type: image/png");
        break;
    case "gif":
        header("Content-Type: image/gif");
        break;
    case "webp":
        header("Content-Type: image/webp");
        break;
    default:
        header("Content-Type: application/octet-stream");
}

header("Cache-Control: public, max-age=86400");

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && $data) {
    echo $data;
} else {
    http_response_code(404);
    exit('Imagem não encontrada');
}