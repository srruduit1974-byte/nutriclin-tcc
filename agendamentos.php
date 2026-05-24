<?php
include 'config.php';

if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: login.php");
    exit();
}

$nutricionista_id = $_SESSION['nutricionista_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $paciente_id = intval($_POST['paciente_id']);
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $observacoes = trim($_POST['observacoes']);

    $data_hora = $data . ' ' . $hora . ':00';

    $sql = "INSERT INTO agendamentos
            (paciente_id, nutricionista_id, data_hora, observacoes)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiss",
        $paciente_id,
        $nutricionista_id,
        $data_hora,
        $observacoes
    );

    $stmt->execute();
}

$sql_pacientes = "
    SELECT id, nome
    FROM pacientes
    WHERE nutricionista_id = ?
    ORDER BY nome
";

$stmtPac = $conn->prepare($sql_pacientes);
$stmtPac->bind_param("i", $nutricionista_id);
$stmtPac->execute();
$pacientes = $stmtPac->get_result();

$sql_agenda = "
    SELECT
        a.*,
        p.nome
    FROM agendamentos a
    INNER JOIN pacientes p
        ON p.id = a.paciente_id
    WHERE a.nutricionista_id = ?
    ORDER BY a.data_hora ASC
";

$stmtAgenda = $conn->prepare($sql_agenda);
$stmtAgenda->bind_param("i", $nutricionista_id);
$stmtAgenda->execute();
$agenda = $stmtAgenda->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Agenda - NutriClin</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
<div class="container">

<a class="navbar-brand fw-bold"
href="dashboard.php">
NutriClin
</a>

<div>
<a href="dashboard.php"
class="btn btn-outline-light btn-sm me-1">
Dashboard
</a>

<a href="pacientes.php"
class="btn btn-outline-light btn-sm me-1">
Pacientes
</a>

<a href="logout.php"
class="btn btn-danger btn-sm">
Sair
</a>
</div>

</div>
</nav>

<div class="container py-5">

<h2 class="text-success mb-4">
📅 Agenda de Consultas
</h2>

<div class="card shadow-sm mb-4">
<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-4 mb-3">
<label class="form-label">Paciente</label>

<select name="paciente_id"
class="form-select"
required>

<option value="">
Selecione
</option>

<?php while($p = $pacientes->fetch_assoc()): ?>

<option value="<?= $p['id'] ?>">
<?= htmlspecialchars($p['nome']) ?>
</option>

<?php endwhile; ?>

</select>
</div>

<div class="col-md-3 mb-3">
<label class="form-label">Data</label>

<input type="date"
name="data"
class="form-control"
required>
</div>

<div class="col-md-3 mb-3">
<label class="form-label">Hora</label>

<input type="time"
name="hora"
class="form-control"
required>
</div>

<div class="col-md-2 mb-3 d-flex align-items-end">

<button type="submit"
class="btn btn-success w-100">

Agendar

</button>

</div>

</div>

<div class="mb-3">
<label class="form-label">
Observações
</label>

<textarea
name="observacoes"
class="form-control"
rows="3"></textarea>
</div>

</form>

</div>
</div>

<div class="card shadow-sm">
<div class="card-body">

<h4>Consultas Agendadas</h4>

<table class="table table-striped">

<thead>
<tr>
<th>Paciente</th>
<th>Data/Hora</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php while($a = $agenda->fetch_assoc()): ?>

<tr>

<td>
<?= htmlspecialchars($a['nome']) ?>
</td>

<td>
<?= date('d/m/Y H:i', strtotime($a['data_hora'])) ?>
</td>

<td>
<?= htmlspecialchars($a['status']) ?>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
