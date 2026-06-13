<?php
$host = "localhost";
$user = "nutri_app";
$pass = "nutrinubia";
$db = "nutriclin_db"; 

// Cria a conexão com o MariaDB
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

// Define o charset padrão para acentuação correta
$conn->set_charset("utf8mb4");
?>

