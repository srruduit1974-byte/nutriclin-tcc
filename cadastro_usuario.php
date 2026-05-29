<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo  = $_POST['tipo'];
    $extra = $_POST['extra']; // CRN ou Matrícula

    // Inserir usuário
    $sql = "INSERT INTO usuarios (email, senha, tipo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $senha, $tipo);

    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;

        // Decide a tabela conforme o tipo
        if ($tipo == 'nutricionista') {
            $sql2 = "INSERT INTO nutricionistas (nome_nutricionista, crn, usuario_id) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ssi", $nome, $extra, $usuario_id);
            $stmt2->execute();
            if (!$stmt2->execute()) {
        echo "Erro ao inserir nutricionista: " . $stmt2->error;
    }
        } else {
            $sql2 = "INSERT INTO estagiarios (nome_estagiario, matricula, usuario_id) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ssi", $nome, $extra, $usuario_id);
            if (!$stmt2->execute()) {
        echo "Erro ao inserir estagiario: " . $stmt2->error;
    }
            $stmt2->execute();
        }

        header("Location: index.php?msg=Cadastro realizado com sucesso");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
}
?>
