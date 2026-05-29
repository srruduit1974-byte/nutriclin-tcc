<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['tipo']       = $usuario['tipo'];
        $_SESSION['user']       = $usuario['email'];

        // Redirecionar conforme tipo
        if ($usuario['tipo'] == 'nutricionista') {
            header("Location: dashboard.php");
        } elseif ($usuario['tipo'] == 'estagiario') {
            header("Location: dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        echo "Login inválido.";
    }
}
?>
