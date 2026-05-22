<?php
function enviarEventoFacebookPurchase($email, $valorCompra, $FacePixel, $FaceToken, $ipUsuario, $fbc, $fbp) {
    $url = 'https://graph.facebook.com/v12.0/' . $FacePixel . '/events?access_token=' . $FaceToken;

    $emailHash = hash('sha256', strtolower(trim($email)));

    $data = [
        'data' => [
            [
                'event_name' => 'Purchase',
                'event_time' => time(),
                'event_source_url' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'user_data' => [
                    'em' => $emailHash,
                    'client_ip_address' => $ipUsuario,
                    'fbc' => $fbc,
                    'fbp' => $fbp,
                ],
                'custom_data' => [
                    'currency' => 'BRL',
                    'value' => $valorCompra
                ]
            ],
        ],
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data, JSON_UNESCAPED_SLASHES),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}