<?php
$host = 'localhost'; // <= Não mexer aqui 
$dbname = 'NOMEDOBANCOAQUI';
$user = 'NOMEDOUSUARIOAQUI';
$pass = 'SENHAAQUI';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Define o modo de erro do PDO para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em produção, você pode registrar isso em um log ao invés de exibir diretamente
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}