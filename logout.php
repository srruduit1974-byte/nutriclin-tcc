<?php
// Logout otimizado - mais rápido

// Destrói a sessão diretamente sem muitos passos
session_start();
session_destroy();

// Limpa cookie de sessão mais simples
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redireciona imediatamente
header("Location: index.php");
exit();
?>
