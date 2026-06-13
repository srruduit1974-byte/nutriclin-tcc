<?php
session_start();
require 'conexao.php';

// Inclui o cabeçalho do Bootstrap caso queira que o alerta apareça formatado na tela
echo '<link href="https://jsdelivr.net" rel="stylesheet">';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo     = $_POST['tipo'];
    $nome     = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email    = $_POST['email'];
    $senha    = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $extra    = $_POST['extra']; // CRN ou matrícula

    // [NOVO] 1. Verificar se o e-mail já existe para evitar erros na tela
    $sqlCheck = "SELECT id FROM usuarios WHERE email = ?"; // Ajuste 'id_usuario' para o nome da sua chave primária
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    if($row = $resultCheck->fetch_assoc()){
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['usuraio_email'] = $row['email'];
    }

    if ($resultCheck->num_rows > 0) {
        echo "<div class='container mt-4'><div class='alert alert-danger'>Este e-mail já está cadastrado!</div></div>";
        echo "<script>setTimeout(function(){ window.history.back(); }, 2000);</script>";
        exit;
    }

    // 2. Inserir usuário
   $sql = "INSERT INTO usuarios (email, senha, tipo) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $email, $senhaHash, $tipo);
$stmt->execute();

    if ($stmtUser->execute()) {
        $usuario_id = $stmtUser->insert_id;

        // 3. Inserir nutricionista ou estagiário
        if ($tipo === 'nutricionista') {
            $sqlNutri = "INSERT INTO nutricionistas (nome, crn, telefone, usuario_id) VALUES (?, ?, ?, ?)";
            $stmtNutri = $conn->prepare($sqlNutri);
            $stmtNutri->bind_param("sssi", $nome, $extra, $telefone, $usuario_id);
            $stmtNutri->execute();
        } elseif ($tipo === 'estagiario') {
            // [AJUSTADO] Adicionado o campo telefone também para o estagiário salvar corretamente
            $sqlEst = "INSERT INTO estagiarios (nome_estagiario, matricula, telefone, usuario_id) VALUES (?, ?, ?, ?)";
            $stmtEst = $conn->prepare($sqlEst);
            $stmtEst->bind_param("sssi", $nome, $extra, $telefone, $usuario_id);
            $stmtEst->execute();
        }

        // Alerta bonito do Bootstrap com redirecionamento automático para a tela de login (index.php)
        echo "<div class='container mt-4'><div class='alert alert-success'>Cadastro realizado com sucesso! Redirecionando...</div></div>";
        echo "<script>setTimeout(function(){ window.location.href='index.php'; }, 2000);</script>";
    } else {
        echo "<div class='container mt-4'><div class='alert alert-danger'>Erro ao cadastrar usuário: {$stmtUser->error}</div></div>";
    }
}
?>


