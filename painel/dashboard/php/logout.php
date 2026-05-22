<?php
session_name('adm_session');
session_start();

// Apaga todos os dados da sessão
$_SESSION = array();

// Se o cookie da sessão existir, destrói ele também
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Apagar o cookie auth_token_adm corretamente (sem subdomínio)
$cookie_options = [
    'expires' => time() - 3600, // expira no passado
    'path' => '/painel/',       // mesma pasta onde o cookie foi definido
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
];
setcookie('auth_token_adm', '', $cookie_options);

// Finalmente, destrói a sessão
session_destroy();

// Redireciona para a pasta do painel
header("Location: /painel/");
exit;
?>