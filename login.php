<?php
session_start();
require 'conexao.php';

// Inclui o Bootstrap para estilizar o erro, caso aconteça
echo '<link href="https://jsdelivr.net" rel="stylesheet">';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 1. Busca o usuário pelo e-mail
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // 2. Valida a senha criptografada
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        
        // Dados básicos de sessão comuns a todos
        $_SESSION['usuario_id'] = $usuario['id']; // Altere para 'id_usuario' se for esse o nome no seu banco
        $_SESSION['tipo']       = $usuario['tipo'];
        $_SESSION['email_user'] = $usuario['email'];

        // 3. Verifica o tipo para carregar as informações do perfil específico
        if ($usuario['tipo'] === 'nutricionista') {
            // [Ajustado] Agora também busca o 'nome' do nutricionista para exibir na tela
            $stmtNutri = $conn->prepare("SELECT id, nome FROM nutricionistas WHERE usuario_id = ?");
            $stmtNutri->bind_param("i", $usuario['id']);
            $stmtNutri->execute();
            $resultadoNutri = $stmtNutri->get_result();
            
            if ($nutri = $resultadoNutri->fetch_assoc()) {
                $_SESSION['nutricionista_id'] = $nutri['id'];
                $_SESSION['user_nome']        = $nutri['nome']; // Guarda o nome real do Nutricionista
            }
            
        } elseif ($usuario['tipo'] === 'estagiario') {
            // [NOVO] Busca as informações necessárias do Estagiário
            $stmtEst = $conn->prepare("SELECT id, nome_estagiario FROM estagiarios WHERE usuario_id = ?");
            $stmtEst = $conn->prepare("SELECT id, nome_estagiario FROM estagiarios WHERE usuario_id = ?"); // Certifique-se de que a PK na tabela chama 'id' ou ajuste aqui
            $stmtEst->bind_param("i", $usuario['id']);
            $stmtEst->execute();
            $resultadoEst = $stmtEst->get_result();
            
            if ($est = $resultadoEst->fetch_assoc()) {
                $_SESSION['estagiario_id'] = $est['id'];
                $_SESSION['user_nome']      = $est['nome_estagiario']; // Guarda o nome real do Estagiário
            }
        }

        // Redireciona com segurança para a dashboard
        header("Location: dashboard.php");
        exit();
        
    } else {
        // Alerta amigável do Bootstrap com botão para tentar novamente
        echo "
        <div class='container mt-5'>
            <div class='alert alert-danger role='alert'>
                <strong>Erro:</strong> E-mail ou senha incorretos!
            </div>
            <button onclick='window.history.back()' class='btn btn-secondary btn-sm'>Voltar e tentar novamente</button>
        </div>";
    }
}
?>
