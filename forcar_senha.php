<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Gera o hash idêntico ao que o seu PHP instalado espera
$senha_pura = "123456";
$hash_perfeito = password_hash($senha_pura, PASSWORD_DEFAULT);

// Atualiza o banco de dados usando o hash gerado pelo próprio servidor
$sql = "UPDATE usuarios SET senha = ? WHERE id = 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hash_perfeito);

if ($stmt->execute()) {
    echo "<h3>✅ Senha atualizada com sucesso pelo próprio servidor!</h3>";
    echo "<b>Hash gerado:</b> " . $hash_perfeito . "<br><br>";
    echo "Apague este arquivo do servidor e tente fazer o login com a senha: <b>123456</b>";
} else {
    echo "❌ Erro ao atualizar o banco: " . $conn->error;
}
?>
