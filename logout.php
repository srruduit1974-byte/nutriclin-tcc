<?php
// Inicia a sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Se existir cookie de sessão, remove também
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Encerra a sessão
session_destroy();

// Redireciona para a tela de login
header("Location: login.php");
exit();
