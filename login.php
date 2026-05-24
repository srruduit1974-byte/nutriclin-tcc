<?php
include 'config.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT 
                u.id AS usuario_id,
                u.email,
                u.senha,
                u.tipo,
                n.id AS nutricionista_id,
                n.nome AS nome_nutricionista
            FROM usuarios u
            INNER JOIN nutricionistas n ON n.usuario_id = u.id
            WHERE u.email = ?
            AND u.tipo = 'nutricionista'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && $senha == $usuario['senha']) {
        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['nutricionista_id'] = $usuario['nutricionista_id'];
        $_SESSION['nome'] = $usuario['nome_nutricionista'];
        $_SESSION['tipo'] = $usuario['tipo'];
        $_SESSION['user'] = $usuario['email'];

        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Login inválido.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - NutriClin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <div class="container mt-5" style="max-width: 450px;">
        <div class="card bg-light text-dark shadow">
            <div class="card-body p-4">

                <h2 class="mb-4 text-center text-success">NutriClin</h2>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>

                    <button class="btn btn-success w-100">Entrar</button>
                </form>

            </div>
        </div>
    </div>

</body>

</html>
