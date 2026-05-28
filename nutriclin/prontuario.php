<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: login.php");
    exit();
}

$nutricionista_id = $_SESSION['nutricionista_id'];

$paciente_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$paciente_id) {
    header("Location: pacientes.php");
    exit();
}

/* ==========================
   PACIENTE
========================== */

$sqlPaciente = "
    SELECT
        id,
        nome,
        cpf,
        telefone,
        data_nascimento,
        sexo
    FROM pacientes
    WHERE id = ?
      AND nutricionista_id = ?
";

$stmtPaciente = $conn->prepare($sqlPaciente);

if (!$stmtPaciente) {
    die("Erro SQL: " . $conn->error);
}

$stmtPaciente->bind_param(
    "ii",
    $paciente_id,
    $nutricionista_id
);

$stmtPaciente->execute();

$paciente = $stmtPaciente
    ->get_result()
    ->fetch_assoc();

if (!$paciente) {
    die("Paciente não encontrado.");
}

/* ==========================
   CONSULTAS
========================== */

$sqlConsultas = "
    SELECT
        id,
        peso,
        altura,
        imc,
        anotacoes,
        data_consulta
    FROM consultas
    WHERE paciente_id = ?
      AND nutricionista_id = ?
    ORDER BY data_consulta DESC
";

$stmtConsultas = $conn->prepare($sqlConsultas);

if (!$stmtConsultas) {
    die("Erro SQL: " . $conn->error);
}

$stmtConsultas->bind_param(
    "ii",
    $paciente_id,
    $nutricionista_id
);

$stmtConsultas->execute();

$consultas = $stmtConsultas->get_result();

function mascararCPF($cpf)
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11) {
        return "Não informado";
    }

    return "***." .
        substr($cpf, 3, 3) .
        "." .
        substr($cpf, 6, 3) .
        "-**";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        Prontuário -
        <?= htmlspecialchars($paciente['nome']) ?>
    </title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold"
           href="dashboard.php">

            <i class="bi bi-heart-pulse-fill me-2"></i>
            NutriClin

        </a>

        <div>

            <a href="pacientes.php"
               class="btn btn-outline-light btn-sm me-2">

                Pacientes

            </a>

            <a href="logout.php"
               class="btn btn-danger btn-sm">

                Sair

            </a>

        </div>

    </div>
</nav>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="text-success fw-bold mb-0">
            Prontuário do Paciente
        </h2>

        <div>

            <a href="nova_avaliacao.php?id=<?= $paciente_id ?>"
               class="btn btn-success">

                <i class="bi bi-plus-circle"></i>
                Nova Avaliação

            </a>

            <a href="pacientes.php"
               class="btn btn-outline-secondary">

                Voltar

            </a>

        </div>

    </div>

    <!-- Dados paciente -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">
                <i class="bi bi-person-vcard me-2"></i>
                <?= htmlspecialchars($paciente['nome']) ?>
            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">

                    <strong>CPF</strong><br>

                    <?= htmlspecialchars(
                        mascararCPF($paciente['cpf'])
                    ) ?>

                </div>

                <div class="col-md-3 mb-3">

                    <strong>Telefone</strong><br>

                    <?= htmlspecialchars(
                        $paciente['telefone'] ?: '-'
                    ) ?>

                </div>

                <div class="col-md-3 mb-3">

                    <strong>Nascimento</strong><br>

                    <?= !empty($paciente['data_nascimento'])
                        ? date(
                            'd/m/Y',
                            strtotime(
                                $paciente['data_nascimento']
                            )
                        )
                        : '-' ?>

                </div>

                <div class="col-md-3 mb-3">

                    <strong>Sexo</strong><br>

                    <?= htmlspecialchars(
                        $paciente['sexo']
                    ) ?>

                </div>

            </div>

        </div>

    </div>

    <!-- Histórico -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-secondary text-white">

            <h5 class="mb-0">
                Histórico de Avaliações
            </h5>

        </div>

        <div class="card-body">

            <?php if ($consultas->num_rows > 0): ?>

                <?php while ($consulta = $consultas->fetch_assoc()): ?>

                    <div class="border rounded p-3 mb-3">

                        <div class="row">

                            <div class="col-md-3">
                                <strong>Data</strong><br>
                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $consulta['data_consulta']
                                    )
                                ) ?>
                            </div>

                            <div class="col-md-3">
                                <strong>Peso</strong><br>
                                <?= $consulta['peso'] ?> kg
                            </div>

                            <div class="col-md-3">
                                <strong>Altura</strong><br>
                                <?= $consulta['altura'] ?> m
                            </div>

                            <div class="col-md-3">
                                <strong>IMC</strong><br>
                                <?= number_format(
                                    $consulta['imc'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </div>

                        </div>

                        <?php if (!empty($consulta['anotacoes'])): ?>

                            <hr>

                            <strong>Observações:</strong>

                            <p class="mb-0">
                                <?= nl2br(
                                    htmlspecialchars(
                                        $consulta['anotacoes']
                                    )
                                ) ?>
                            </p>

                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="alert alert-info mb-0">

                    Nenhuma avaliação cadastrada.

                </div>

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
