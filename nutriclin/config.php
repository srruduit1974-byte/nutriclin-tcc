<?php
// 1. Sempre inicie ou verifique a sessão antes de qualquer outra lógica
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "nutrinubia";
$db = "nutriclin_db"; 

// 2. Cria a conexão com o MariaDB
$conn = new mysqli($host, $user, $pass, $db);

// 3. Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

// 4. Define o charset padrão para acentuação correta
$conn->set_charset("utf8mb4");
?>

