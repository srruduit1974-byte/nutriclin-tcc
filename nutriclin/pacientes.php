<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: login.php");
    exit();
}

$nutricionista_id = $_SESSION['nutricionista_id'];
$mensagem = "";

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    $sqlDelPaciente = "DELETE FROM pacientes WHERE id = ? AND nutricionista_id = ?";
    $stmtDelPaciente = $conn->prepare($sqlDelPaciente);
    $stmtDelPaciente->bind_param("ii", $delete_id, $nutricionista_id);

    if ($stmtDelPaciente->execute()) {
        $mensagem = "<div class='alert alert-success'>Paciente excluído com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao excluir paciente.</div>";
    }
    $stmtDelPaciente->close();
}

// --- LISTAGEM ---
$sql_lista = "SELECT * FROM pacientes WHERE nutricionista_id = ? ORDER BY nome ASC";
$stmt_lista = $conn->prepare($sql_lista);
$stmt_lista->bind_param("i", $nutricionista_id);
$stmt_lista->execute();
$pacientes = $stmt_lista->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - NutriClin</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-heart-pulse-fill me-2"></i>NutriClin
        </a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm me-1">Dashboard</a>
            <a href="agendamentos.php" class="btn btn-outline-light btn-sm me-1">Agenda</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="text-success mb-4 fw-bold">Pacientes cadastrados</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-success fw-bold">Pacientes cadastrados</h2>
    <a href="novo_paciente.php" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Novo Paciente
    </a>
</div>


    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>LGPD</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pacientes->num_rows > 0): ?>
                            <?php while ($paciente = $pacientes->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($paciente['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($paciente['cpf'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($paciente['telefone'] ?: '-'); ?></td>
                                    <td>
                                        <?php if ($paciente['aceite_lgpd']): ?>
                                            <span class="badge bg-success">Aceito</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <<td>
    <a href="prontuario.php?id=<?php echo $paciente['id']; ?>" 
       class="btn btn-sm btn-success me-1">
       <i class="bi bi-file-earmark-text me-1"></i>Prontuário
    </a>

    <a href="nova_avaliacao.php?id=<?php echo $paciente['id']; ?>" 
       class="btn btn-sm btn-warning me-1">
       <i class="bi bi-plus-circle me-1"></i>Avaliação
    </a>

    <a href="pacientes.php?delete=<?php echo $paciente['id']; ?>" 
       class="btn btn-sm btn-danger"
       onclick="return confirm('Tem certeza que deseja excluir este paciente?');">
       <i class="bi bi-trash me-1"></i>Excluir
    </a>
</td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Nenhum paciente cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
