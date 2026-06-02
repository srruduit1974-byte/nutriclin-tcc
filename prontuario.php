<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php'; // Adicionado para garantir a existência do $conn
require_once 'config.php';

// [CORRIGIDO] Se não estiver logado, redireciona para a tela correta (index.php)
if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: index.php");
    exit();
}

$nutricionista_id = $_SESSION['nutricionista_id'];

$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$paciente_id) {
    header("Location: pacientes.php");
    exit();
}

/* ==========================
   PACIENTE
========================== */
$sqlPaciente = "
    SELECT id, nome, cpf, telefone, data_nascimento, sexo
    FROM pacientes
    WHERE id = ? AND nutricionista_id = ?
";

$stmtPaciente = $conn->prepare($sqlPaciente);

if (!$stmtPaciente) {
    die("Erro SQL: " . $conn->error);
}

$stmtPaciente->bind_param("ii", $paciente_id, $nutricionista_id);
$stmtPaciente->execute();
$paciente = $stmtPaciente->get_result()->fetch_assoc();

if (!$paciente) {
    die("<div class='container mt-5 alert alert-danger'>Paciente não encontrado ou acesso negado.</div>");
}

/* ==========================
   CONSULTAS
========================== */
$sqlConsultas = "
    SELECT id, peso, altura, imc, anotacoes, data_consulta
    FROM consultas
    WHERE paciente_id = ? AND nutricionista_id = ?
    ORDER BY data_consulta DESC
";

$stmtConsultas = $conn->prepare($sqlConsultas);

if (!$stmtConsultas) {
    die("Erro SQL: " . $conn->error);
}

$stmtConsultas->bind_param("ii", $paciente_id, $nutricionista_id);
$stmtConsultas->execute();
$consultas = $stmtConsultas->get_result();

// Função que ampara as boas práticas de LGPD no seu TCC
function mascararCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) {
        return "Não informado";
    }
    return "***." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-**";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prontuário - <?= htmlspecialchars($paciente['nome']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-heart-pulse-fill me-2"></i>NutriClin
        </a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
            <a href="pacientes.php" class="btn btn-outline-light btn-sm me-2">Pacientes</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold mb-0">Prontuário do Paciente</h2>
        <div>
            <a href="nova_avaliacao.php?id=<?= $paciente_id ?>" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nova Avaliação
            </a>
            <a href="pacientes.php" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <!-- DADOS DO PACIENTE -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-person-vcard me-2"></i><?= htmlspecialchars($paciente['nome']) ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <strong>CPF</strong><br>
                    <?= htmlspecialchars(mascararCPF($paciente['cpf'])) ?>
                </div>
                <div class="col-md-3 mb-3">
                    <strong>Telefone</strong><br>
                    <?= htmlspecialchars($paciente['telefone'] ?: '-') ?>
                </div>
                <div class="col-md-3 mb-3">
                    <strong>Nascimento</strong><br>
                    <?= !empty($paciente['data_nascimento']) ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : '-' ?>
                </div>
                <div class="col-md-3 mb-3">
                    <strong>Sexo</strong><br>
                    <?= htmlspecialchars($paciente['sexo']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTÓRICO DE AVALIAÇÕES -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Histórico de Avaliações</h5>
        </div>
        <div class="card-body">
            <?php if ($consultas->num_rows > 0): ?>
                <?php while ($consulta = $consultas->fetch_assoc()): ?>
                    <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Data / Hora</strong><br>
                                <span class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($consulta['data_consulta'])) ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Peso</strong><br>
                                <?= number_format($consulta['peso'], 2, ',', '.') ?> kg
                            </div>
                            <div class="col-md-3">
                                <strong>Altura</strong><br>
                                <?= number_format($consulta['altura'], 2, ',', '.') ?> m
                            </div>
                            <div class="col-md-3">
                                <strong>IMC</strong><br>
                                <span class="badge bg-success fs-6"><?= number_format($consulta['imc'], 2, ',', '.') ?></span>
                            </div>
                        </div>

                        <?php if (!empty($consulta['anotacoes'])): ?>
                            <hr>
                            <strong>Anotações / Conduta Clínica:</strong>
                            <p class="mb-0 text-secondary mt-1">
                                <?= nl2br(htmlspecialchars($consulta['anotacoes'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0">Nenhuma avaliação antropométrica cadastrada para este paciente.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmtPaciente->close();
$stmtConsultas->close();
$conn->close();
?>
