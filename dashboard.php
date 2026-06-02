<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';
require 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); 
    exit();
}

// Pega dados da sessão
$tipo = $_SESSION['tipo'] ?? 'desconhecido';
$nome = $_SESSION['user_nome'] ?? $_SESSION['email_user'] ?? 'Usuário'; 
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NutriClin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- BARRA DE NAVEGAÇÃO -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">NutriClin</a>

            <div class="d-flex align-items-center text-white gap-2">
                <span class="me-2">
                    Olá, <strong><?php echo htmlspecialchars($nome); ?></strong> 
                    <span class="badge bg-light text-success ms-1"><?php echo ucfirst($tipo); ?></span>
                </span>
                <a href="agendamentos.php" class="btn btn-primary btn-sm">Abrir Agenda</a>
                <!-- LINK ADICIONADO NA NAV -->
                <a href="relatorios.php" class="btn btn-warning btn-sm text-dark fw-bold">Relatórios</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="container py-5">

        <h2 class="mb-4 text-success fw-bold">Painel de Controle</h2>

        <div class="row g-4">

            <!-- CARD: PACIENTES -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mt-2">👥 Pacientes</h5>
                            <p class="card-text text-muted">Cadastro e gerenciamento de pacientes do sistema.</p>
                        </div>
                        <a href="pacientes.php" class="btn btn-success w-100 mt-3">Acessar Pacientes</a>
                    </div>
                </div>
            </div>

            <!-- CARD: NOVO PACIENTE -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mt-2">➕ Novo Paciente</h5>
                            <p class="card-text text-muted">Cadastrar um novo paciente na sua base de dados.</p>
                        </div>
                        <a href="novo_paciente.php" class="btn btn-success w-100 mt-3">Cadastrar Paciente</a>
                    </div>
                </div>
            </div>

            <!-- CARD: NOVA AVALIAÇÃO -->
            <?php if ($tipo === 'nutricionista'): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title fw-bold mt-2">📋 Nova Avaliação</h5>
                                <p class="card-text text-muted">Registrar peso, altura, IMC e a evolução clínica.</p>
                            </div>
                            <a href="pacientes.php?id=1" class="btn btn-warning text-white w-100 mt-3">Nova Avaliação</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CARD: PRONTUÁRIOS -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mt-2">📑 Prontuários</h5>
                            <p class="card-text text-muted">Consultar o histórico completo e evolução dos pacientes.</p>
                        </div>
                        <a href="pacientes.php" class="btn btn-primary w-100 mt-3">Abrir Prontuários</a>
                    </div>
                </div>
            </div>

            <!-- CARD NOVO: RELATÓRIOS GERENCIAIS (Apenas Nutricionista vê) -->
            <?php if ($tipo === 'nutricionista'): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title fw-bold mt-2">📊 Relatórios de Atendimento</h5>
                                <p class="card-text text-muted">Imprimir balanço diário, mensal ou anual de consultas.</p>
                            </div>
                            <a href="relatorios.php" class="btn btn-dark w-100 mt-3">Gerar Relatórios</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div> <!-- Fim da row -->
    </div> <!-- Fim do container -->

</body>
</html>
