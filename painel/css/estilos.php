<?php
header("Content-type: text/css");
include '../../includes/db.php';
include '../../includes/config.php';
?>

	* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
body {
    background-color: #121212;
    color: #fff;
    display: flex;
    flex-direction: column; 
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 10px;
}
.login-container {
    background-color: #1E1E1E;
    border-radius: 5px;
    width: 100%;
    max-width: 400px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
    text-align: center;
}
.logo {
    display: block;
    margin: 40px auto;
    width: 150px;
}
.login-container h2 {
    margin-bottom: 25px;
    color: <?= $corPrincipal ?>;
}
.input-group {
    position: relative;
    margin-bottom: 5px;
    text-align: left;
}
.input-group i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: <?= $corPrincipal ?>;
    font-size: 16px;
}
.input-group input {
    padding-left: 35px;
    width: 100%;
    height: 45px;
    border: none;
    border-radius: 3px;
    color: #fff;
    background-color: #121212;
    outline: none;
    box-sizing: border-box;
    font-size: 14px;
}
.input-group input::placeholder {
    font-size: 14px;
}
.login-btn {
    width: 100%;
    padding: 15px 20px;
    background-color: <?= $corPrincipal ?>;
    color: <?= $corTexto ?>;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    margin-top: 12px;
    font-size: 16px;
    font-weight: bold;
    transition: background 0.3s;
}
.login-btn:hover {
    background-color: <?= $corHover ?>;
}
.footer {
    margin-top: 20px;
    font-size: 14px;
    color: #555;
    text-align: center;
}
#alerta-login{
    width: 100%;
    margin-top: -10px;
    margin-bottom: 10px;
    display: none;
    font-size: 14px;
}

.alertanao {
    width: 100%;
    color: #FE0000;
    text-align: center;
    display: block;
    cursor: pointer;
    line-height: 24px;
}

.alertasim {
    width: 100%;
    color: <?= $corPrincipal ?>;
    text-align: center;
    display: block;
    cursor: pointer;
    line-height: 24px;
}

/* CSS para Mobile */
@media (max-width: 480px) {
    body {
        height: auto;
        padding: 20px 20px;
    }

    .logo {
        width: 120px;
        margin: 20px auto 30px auto;
    }

    .login-container {
        padding: 15px;
        max-width: 100%;
        box-shadow: none;
        border-radius: 0;
        width: 100%;
    }

    .login-btn {
         padding: 11px 22px;
                font-size: 17px;
    }

    .input-group input {
        font-size: 16px;
    }
}