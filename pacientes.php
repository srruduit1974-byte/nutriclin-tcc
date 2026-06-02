<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$tipo = $_SESSION['tipo'] ?? '';
$nutricionista_id = $_SESSION['nutricionista_id'] ?? null;
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (isset($_GET['delete'])) {
    $id_excluir = (int)$_GET['delete'];
    $sqlDelete = "DELETE FROM pacientes WHERE id = ?";
    $stmtDel = $conn->prepare($sqlDelete);
    $stmtDel->bind_param("i", $id_excluir);
    if ($stmtDel->execute()) {
        echo "<script>alert('Paciente excluído com sucesso!'); window.location.href='pacientes.php';</script>";
        exit();
    }
}

if ($tipo === 'nutricionista') {
    $sql = "SELECT id, nome, cpf, telefone, aceite_lgpd,
                   (SELECT MIN(data_hora) FROM agendamentos WHERE paciente_id = pacientes.id AND data_hora >= NOW() AND status = 'agendada') AS proxima_consulta
            FROM pacientes WHERE nutricionista_id = ?";
    if (!empty($busca)) { $sql .= " AND (nome LIKE ? OR cpf LIKE ?)"; }
    $sql .= " ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    if (!empty($busca)) {
        $termo = "%$busca%";
        $stmt->bind_param("iss", $nutricionista_id, $termo, $termo);
    } else {
        $stmt->bind_param("i", $nutricionista_id);
    }
    $stmt->execute();
    $pacientes = $stmt->get_result();
} else {
    $sql = "SELECT id, nome, cpf, telefone, aceite_lgpd,
                   (SELECT MIN(data_hora) FROM agendamentos WHERE paciente_id = pacientes.id AND data_hora >= NOW() AND status = 'agendada') AS proxima_consulta
            FROM pacientes";
    if (!empty($busca)) { $sql .= " WHERE (nome LIKE ? OR cpf LIKE ?)"; }
    $sql .= " ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    if (!empty($busca)) {
        $termo = "%$busca%";
        $stmt->bind_param("ss", $termo, $termo);
        $stmt->execute();
        $pacientes = $stmt->get_result();
    } else {
        $pacientes = $conn->query($sql);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - NutriClin</title>
    <!-- CSS Embutido de Emergência para garantir a estética mesmo offline -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; color: #333; }
        .navbar { background-color: #198754; padding: 15px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: white; }
        .nav-container { max-width: 1140px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
        .navbar-brand { font-weight: bold; font-size: 1.25rem; color: white; text-decoration: none; }
        .nav-btn { color: white; border: 1px solid rgba(255,255,255,0.5); padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-left: 5px; font-size: 0.875rem; transition: 0.2s; }
        .nav-btn:hover { background: rgba(255,255,255,0.2); }
        .btn-danger-nav { background-color: #dc3545; border-color: #dc3545; }
        .btn-danger-nav:hover { background-color: #bb2d3b; }
        .container { max-width: 1140px; margin: 40px auto; padding: 0 20px; }
        h2 { color: #198754; font-weight: bold; margin-bottom: 25px; }
        .row-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; }
        .btn-success { background-color: #198754; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-block; border: none; cursor: pointer; }
        .btn-success:hover { background-color: #157347; }
        .search-form { display: flex; gap: 10px; flex-grow: 1; max-width: 600px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        .btn-primary { background-color: #0d6efd; color: white; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-secondary { background-color: #6c757d; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; }
        .card { background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; }
        td { padding: 15px 12px; border-bottom: 1px solid #dee2e6; }
        tr:hover { background-color: rgba(0,0,0,0.02); }
        .fw-semibold { font-weight: 600; }
        .badge { display: inline-block; padding: 6px 10px; font-size: 0.75rem; font-weight: 700; border-radius: 4px; text-align: center; color: white; }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #212529; }
        .text-muted { color: #6c757d; }
        .btn-sm { padding: 5px 10px; font-size: 0.875rem; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-warning-action { background-color: #ffc107; color: #212529; }
        .btn-warning-action:hover { background-color: #ffca2c; }
        .btn-danger-action { background-color: #dc3545; color: white; }
        .btn-danger-action:hover { background-color: #bb2d3b; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a class="navbar-brand" href="dashboard.php">NutriClin</a>
        <div>
            <a href="dashboard.php" class="nav-btn">Dashboard</a>
            <a href="agendamentos.php" class="nav-btn">Agenda</a>
            <a href="logout.php" class="nav-btn btn-danger-nav">Sair</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Pacientes cadastrados</h2>
    
    <div class="row-actions">
        <div>
            <a href="novo_paciente.php" class="btn btn-success">Novo Paciente</a>
        </div>
        <form action="pacientes.php" method="GET" class="search-form">
            <input type="text" name="busca" class="form-control" placeholder="Buscar por Nome ou CPF..." value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if (!empty($busca)): ?>
                <a href="pacientes.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Próxima Consulta</th>
                    <th>LGPD</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pacientes && $pacientes->num_rows > 0): ?>
                    <?php while ($paciente = $pacientes->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($paciente['nome']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['cpf'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($paciente['telefone'] ?: '-'); ?></td>
                            <td>
                                <?php 
                                if (!empty($paciente['proxima_consulta'])) {
                                    $data_formatada = date('d/m/Y \à\s H:i', strtotime($paciente['proxima_consulta']));
                                    echo "<span class='badge bg-primary'>" . $data_formatada . "</span>";
                                } else {
                                    echo "<span class='text-muted' style='font-size: 0.9rem;'>Nenhuma agendada</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($paciente['aceite_lgpd']): ?>
                                    <span class="badge bg-success">Aceito</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="prontuario.php?id=<?php echo $paciente['id']; ?>" class="btn-sm btn-success">Prontuário</a>
                                <a href="nova_avaliacao.php?id=<?php echo $paciente['id']; ?>" class="btn-sm btn-warning-action">Avaliação</a>
                                <a href="pacientes.php?delete=<?php echo $paciente['id']; ?>" class="btn-sm btn-danger-action" onclick="return confirm('Tem certeza que deseja excluir este paciente?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6c757d; padding: 30px;">Nenhum paciente encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
