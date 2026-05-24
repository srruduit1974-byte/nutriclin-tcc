<?php
// Correção de segurança: inicia a sessão para o sistema ler o nutricionista_id
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $data_nascimento = $_POST['data_nascimento'] ?: null;
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $sexo = $_POST['sexo'] ?? 'OUTRO';

    $aceite_lgpd = isset($_POST['aceite_lgpd']) ? 1 : 0;
    $data_aceite = $aceite_lgpd ? date("Y-m-d H:i:s") : null;

    try {
        $sql = "INSERT INTO pacientes 
                (nome, data_nascimento, cpf, telefone, sexo, aceite_lgpd, data_aceite, nutricionista_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssisi",
            $nome,
            $data_nascimento,
            $cpf,
            $telefone,
            $sexo,
            $aceite_lgpd,
            $data_aceite,
            $nutricionista_id
        );

        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Paciente cadastrado com sucesso!</div>";
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $mensagem = "<div class='alert alert-danger'>CPF já cadastrado.</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
        }
    }
}

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
    <h2 class="text-success mb-4 fw-bold">Pacientes</h2>
    <?php echo $mensagem; ?>

    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body">
            <h4 class="mb-3 fw-semibold">Novo Paciente</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome completo</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data de nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-select">
                            <option value="OUTRO">Outro / Não informar</option>
                            <option value="F">Feminino</option>
                            <option value="M">Masculino</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="aceite_lgpd" id="aceite_lgpd" class="form-check-input">
                    <label for="aceite_lgpd" class="form-check-label">Paciente autorizou o uso dos dados conforme LGPD.</label>
                </div>
                <button type="submit" class="btn btn-success px-4">Salvar Paciente</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-3 fw-semibold">Pacientes cadastrados</h4>
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
                                    <td>
                                        <a href="prontuario.php?id=<?php echo $paciente['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-file-earmark-text me-1"></i>Prontuário
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
