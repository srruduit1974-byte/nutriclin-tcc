<?php
session_start();
require 'conexao.php';
require 'config.php';

// Se não estiver logado, volta para login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Processa ações
if (isset($_GET['concluir'])) {
    $id = (int)$_GET['concluir'];
    $conn->query("UPDATE agendamentos SET status='concluida' WHERE id=$id");
}
if (isset($_GET['cancelar'])) {
    $id = (int)$_GET['cancelar'];
    $conn->query("UPDATE agendamentos SET status='cancelada' WHERE id=$id");
}

// Busca agendamentos do nutricionista logado
$sqlAgendamentos = "SELECT a.id, p.nome, a.data_hora, a.observacoes, a.status
                    FROM agendamentos a
                    JOIN pacientes p ON a.paciente_id = p.id
                    WHERE a.nutricionista_id = (
                        SELECT id FROM nutricionistas WHERE usuario_id = ?
                    )
                    ORDER BY a.data_hora ASC";
$stmt = $conn->prepare($sqlAgendamentos);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultAgendamentos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NutriClin - Agendamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { background-color: #2ecc71; }
        .navbar-brand, .nav-link { color: white !important; }
        h2 { color: #2ecc71; margin-top: 20px; }
    </style>
</head>
<body>


    <!-- Cabeçalho igual ao dashboard -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">NutriClin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="listar_agendamentos.php">Agenda</a></li>
                    <li class="nav-item"><a class="nav-link" href="agendamentos.php">Novo Agendamento</a></li>
                    <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Agendamentos</h2>
        <table class="table table-striped">
            <thead class="table-success">
                <tr>
                    <th>Paciente</th>
                    <th>Data/Hora</th>
                    <th>Observações</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while($ag = $resultAgendamentos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ag['nome']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($ag['data_hora'])); ?></td>
                    <td><?php echo htmlspecialchars($ag['observacoes']); ?></td>
                    <td>
                        <?php 
                            if ($ag['status'] == 'concluida') {
                                echo "<span class='badge bg-success'>Concluída</span>";
                            } elseif ($ag['status'] == 'cancelada') {
                                echo "<span class='badge bg-danger'>Cancelada</span>";
                            } else {
                                echo "<span class='badge bg-secondary'>Agendada</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="listar_agendamentos.php?concluir=<?php echo $ag['id']; ?>" 
                           class="btn btn-success btn-sm">✔ Concluir</a>
                        <a href="listar_agendamentos.php?cancelar=<?php echo $ag['id']; ?>" 
                           class="btn btn-danger btn-sm">✖ Cancelar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include 'rodape.php'; ?>
</body>
</html>
