<?php
include 'config.php';

if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: login.php");
    exit();
}

$nome = $_SESSION['nome'] ?? 'Nutricionista';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - NutriClin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">NutriClin</a>

            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    Olá, <strong><?php echo htmlspecialchars($nome); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
                <a href="agendamentos.php" class="btn btn-primary w-100"> 
    Abrir Agenda
</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <h2 class="mb-4 text-success">Painel de Controle</h2>

        <div class="row g-4">

            <div class="row g-4">
<div class="row g-4">

    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body text-center">

                <h5 class="card-title">👥 Pacientes</h5>

                <p class="card-text">
                    Cadastro e gerenciamento de pacientes.
                </p>

                <a href="pacientes.php"
                   class="btn btn-success w-100">
                    Acessar Pacientes
                </a>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body text-center">

                <h5 class="card-title">📋 Nova Avaliação</h5>

                <p class="card-text">
                    Registrar peso, altura, IMC e evolução clínica.
                </p>

                <a href="pacientes.php"
                   class="btn btn-warning w-100">
                    Iniciar Avaliação
                </a>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body text-center">

                <h5 class="card-title">📑 Prontuários</h5>

                <p class="card-text">
                    Consultar histórico e evolução dos pacientes.
                </p>

                <a href="pacientes.php"
                   class="btn btn-primary w-100">
                    Abrir Prontuários
                </a>

            </div>
        </div>
    </div>

</div>
   
    </div>

</body>

</html>
