<?php
// Impede acesso direto via navegador (GET)
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    header("Location: /");
    exit;
}

session_start();

require_once '../includes/db.php';
require_once '../includes/config.php';

// Função para validar o CPF
function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    if (strlen($cpf) != 11) {
        return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

// Função para calcular a idade com base na data de nascimento
function calcularIdade($nascimento) {
    $data_nascimento = DateTime::createFromFormat('d/m/Y', $nascimento);
    if ($data_nascimento === false) {
        return false;
    }

    $hoje = new DateTime();
    $idade = $hoje->diff($data_nascimento)->y;
    return $idade;
}

function enviarEventoFacebook($email, $FacePixel, $FaceToken) {
    $url = 'https://graph.facebook.com/v12.0/' . $FacePixel . '/events?access_token=' . $FaceToken;

    $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';
    $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';

    $emailHash = hash('sha256', strtolower(trim($email)));
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $data = [
        'data' => [
            [
                'event_name' => 'CompleteRegistration',
                'event_time' => time(),
                'event_source_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/?modal=cadastro&utm=facebook',
                'user_data' => [
                    'em' => $emailHash,
                    'client_ip_address' => $clientIp,
                    'client_user_agent' => $userAgent,
                    'fbc' => $fbc,
                    'fbp' => $fbp,
                ],
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


// Função para validar CSRF dinamicamente
function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    $data = array(
    "nome"     => htmlspecialchars(trim(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
    "email"    => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL), 
    "cpf"      => htmlspecialchars(trim(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
    "celular"  => htmlspecialchars(trim(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES,'UTF-8'),
    "nascimento" => htmlspecialchars(trim(filter_input(INPUT_POST, "nascimento", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
    "senha"      => htmlspecialchars(trim(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
    "confirmasenha" => htmlspecialchars(trim(filter_input(INPUT_POST, "confirmasenha", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8'),
    "ref" => (int) filter_input(INPUT_POST, "ref", FILTER_SANITIZE_NUMBER_INT),
    "utm"          => htmlspecialchars(trim(filter_input(INPUT_POST, "utm", FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8')
);

// ======================
// VALIDAÇÕES PRINCIPAIS
// ======================
if (!valida_token_csrf('cadastro')) {
    $errors[] = "Falha. Por favor, tente novamente.";

} else if (empty($data["nome"])) {
    $errors[] = "O campo nome é obrigatório!";

} else if (str_word_count($data["nome"]) < 2) {
    $errors[] = "Informe o nome completo!";

} else if (empty($data["email"])) {
    $errors[] = "O campo email é obrigatório!";

} else if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email inválido!";

} else if (empty($data["cpf"])) {
    $errors[] = "O campo CPF é obrigatório!";

} else if (!validaCPF($data["cpf"])) {
    $errors[] = "CPF inválido!";
}

// ======================
// CAMPO CELULAR (CONDICIONAL)
// ======================
if ($bet_campocel === 1 && empty($errors)) {

    if (empty($data["celular"])) {
        $errors[] = "O campo celular é obrigatório!";
    } else {
        $celularLimpo = preg_replace('/\D/', '', $data["celular"]);

        if (strlen($celularLimpo) < 10 || strlen($celularLimpo) > 11) {
            $errors[] = "Celular inválido!";
        }
    }
}

// ======================
// CPF JÁ CADASTRADO
// ======================
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bet_usuarios WHERE bet_cpf = ?");
    $stmt->execute([$data["cpf"]]);

    if ($stmt->fetchColumn() > 0) {
        $errors[] = "CPF já cadastrado!";
    }
}


    // Verificação de dados de entrada
if (empty($data["nascimento"])) {
    $errors[] = "O campo nascimento é obrigatório!";
} else if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data["nascimento"])) {
    $errors[] = "Formato inválido! Use dd/mm/aaaa.";
} else {
    // Separa o dia, mês e ano
    $partes = explode('/', $data["nascimento"]);

    if (count($partes) !== 3) {
        $errors[] = "Data de nascimento em formato inválido!";
    } else {
        list($dia, $mes, $ano) = $partes;

        // Converte os valores para inteiros
        $dia = (int)$dia;
        $mes = (int)$mes;
        $ano = (int)$ano;

        // Verifica se a data é válida
        if (!checkdate($mes, $dia, $ano)) {
            $errors[] = "Data de nascimento inválida!";
        } else {
            // Verifica se a data está no futuro
            $data_nascimento = DateTime::createFromFormat('d/m/Y', $data["nascimento"]);
            $hoje = new DateTime();

            if ($data_nascimento > $hoje) {
                $errors[] = "Nascimento não pode ser uma data futura!";
            } else {
                $idade = calcularIdade($data["nascimento"]);
                if ($idade === false) {
                    $errors[] = "Data de nascimento inválida!";
                } else if ($idade < 18) {
                    $errors[] = "Você deve ter pelo menos 18 anos!";
                }
            }
        }
    }
}

    if (empty($data["senha"])) {
        $errors[] = "O campo senha é obrigatório!";
    } else if (strlen($data["senha"]) < 8) {
        $errors[] = "A senha deve ter no mínimo 8 caracteres!";
    }

    if (empty($data["confirmasenha"])) {
        $errors[] = "O campo confirmar senha é obrigatório!";
    } else if ($data["senha"] != $data["confirmasenha"]) {
        $errors[] = "Confirmação de senha não confere!";
    }

    // Resposta de sucesso ou erro
    if (count($errors) > 0) {
        $response = array(
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . $errors[0] . " <span><i class='fas fa-times'></i></span></p>"
        );
    } else {

        $senha_hash = password_hash($data["senha"], PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $ip_usuario = $_SERVER['REMOTE_ADDR'];

// =========================
// MONTA INSERT DINÂMICO
// =========================
$campos = [
    'bet_nome',
    'bet_email',
    'bet_cpf',
    'bet_nascimento',
    'bet_senha',
    'bet_token',
    'bet_ip',
    'bet_ref',
    'bet_origem',
    'bet_data',
    'bet_status'
];

$valores = [
    ':nome',
    ':email',
    ':cpf',
    ':nascimento',
    ':senha',
    ':token',
    ':ip',
    ':ref',
    ':origem',
    'NOW()',
    '1'
];

// Se o campo celular estiver ativo, adiciona
if ($bet_campocel === 1) {
    $campos[]  = 'bet_celular';
    $valores[] = ':celular';
}

// Monta SQL
$sql = "
INSERT INTO bet_usuarios (" . implode(', ', $campos) . ")
VALUES (" . implode(', ', $valores) . ")
";

$stmt = $pdo->prepare($sql);

// =========================
// BINDS
// =========================
$stmt->bindParam(':nome',       $data["nome"]);
$stmt->bindParam(':email',      $data["email"]);
$stmt->bindParam(':cpf',        $data["cpf"]);
$stmt->bindParam(':nascimento', $data["nascimento"]);
$stmt->bindParam(':senha',      $senha_hash);
$stmt->bindParam(':token',      $token);
$stmt->bindParam(':ip',         $ip_usuario);
$stmt->bindParam(':ref',        $data["ref"], PDO::PARAM_INT);
$stmt->bindParam(':origem',     $data["utm"]);

// Bind do celular somente se ativo
if ($bet_campocel === 1) {
    $celularLimpo = preg_replace('/\D/', '', $data["celular"]);
    $stmt->bindParam(':celular', $celularLimpo);
}

$stmt->execute();

$novo_usuario_id = $pdo->lastInsertId();


if ($ValorBonusCadastro > 0) {
    $stmt_bonus = $pdo->prepare("INSERT INTO bet_bonus (bet_usuario, bet_bonus_tipo, bet_bonus_valor, bet_bonus_status, bet_data) VALUES (:usuario, 'Cadastro', :valor, 0, NOW())");
    $stmt_bonus->bindParam(':usuario', $novo_usuario_id, PDO::PARAM_INT);
    $stmt_bonus->bindParam(':valor', $ValorBonusCadastro);
    $stmt_bonus->execute();
}

$cookie_options = [
    'expires' => time() + 31536000, // 1 ano
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'], 
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
];

setcookie('auth_token', $token, $cookie_options);


            if (!empty($data['utm']) && $data['utm'] === 'facebook' && !empty($FacePixel) && !empty($FaceToken)) {
                    $resultado_facebook = enviarEventoFacebook($data['email'], $FacePixel, $FaceToken);
            }


        $successMessage = "Cadastro realizado com sucesso! Aguardem...";
        $response = array(
            "status" => "alertasim",
            "message" => "<p class='alertasim'>{$successMessage} <span><i class='fas fa-check'></i></span></p>"
        );

        // Regenera o token CSRF após um envio bem-sucedido
        $_SESSION['csrf_token_cadastro'] = bin2hex(random_bytes(32));

           
    }

    // Envia a resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}