<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require 'conexao.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $sql = "SELECT u.id, u.senha, u.tipo,
                   n.id AS nutricionista_id,
                   COALESCE(n.nome, u.email) AS nome
            FROM usuarios u
            LEFT JOIN nutricionistas n ON n.usuario_id = u.id
            WHERE u.email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($senha, $row['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']       = $row['id'];
            $_SESSION['usuario_tipo']     = $row['tipo'] ?? 'desconhecido';
            $_SESSION['nutricionista_id'] = $row['nutricionista_id'];
            $_SESSION['user_nome']        = $row['nome'];   // nunca será NULL por causa do COALESCE
            $_SESSION['email_user']       = $email;         // fallback explícito
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: index.php?erro=1");
            exit();
        }
    } else {
        header("Location: index.php?erro=1");
        exit();
    }

    $stmt->close();
}
?>
