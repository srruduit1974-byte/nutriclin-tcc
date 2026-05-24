<?php
include 'config.php';

if (!isset($_SESSION['nutricionista_id'])) {
    header("Location: login.php");
    exit();
}

$nutricionista_id = $_SESSION['nutricionista_id'];
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$paciente_id) {
    header("Location: pacientes.php");
    exit();
}

$sql = "SELECT * FROM pacientes 
        WHERE id = ? 
        AND nutricionista_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $paciente_id, $nutricionista_id);
$stmt->execute();

$paciente = $stmt->get_result()->fetch_assoc();

if (!$paciente) {
    die("Paciente não encontrado ou acesso negado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Nova Avaliação - <?php echo htmlspecialchars($paciente['nome']); ?></title>
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
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-heart-pulse-fill me-2"></i>NutriClin
        </a>
        <div>
            <a href="prontuario.php?id=<?php echo $paciente_id; ?>" class="btn btn-outline-light btn-sm me-1">
                <i class="bi bi-arrow-left-short"></i> Voltar ao Prontuário
            </a>
            <a href="pacientes.php" class="btn btn-outline-light btn-sm me-1">Pacientes</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="card shadow-sm mx-auto border-0" style="max-width: 650px;">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-calculator me-2"></i>Nova Avaliação Antropométrica
            </h4>
        </div>

        <div class="card-body bg-white p-4">
            
            <div class="alert alert-secondary border-0 bg-light d-flex align-items-center mb-4">
                <i class="bi bi-person-circle fs-3 text-success me-3"></i>
                <div>
                    <span class="text-muted small d-block">PACIENTE EM ATENDIMENTO</span>
                    <strong class="fs-5 text-dark"><?php echo htmlspecialchars($paciente['nome']); ?></strong>
                </div>
            </div>

            <form method="POST" action="processa_imc.php">

                <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Peso atual (kg)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-speedometer2"></i></span>
                            <input type="number" step="0.01" min="1" max="500" name="peso" id="peso" class="form-control form-control-lg" placeholder="Ex: 120.00" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Altura (metros)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-ruler"></i></span>
                            <input type="number" step="0.01" min="0.50" max="3.00" name="altura" id="altura" class="form-control form-control-lg" placeholder="Ex: 1.74" required>
                        </div>
                    </div>
                </div>

                <div id="painel-imc" class="card my-4 border-0 bg-light d-none">
                    <div class="card-body text-center py-3">
                        <p class="mb-1 text-muted small fw-bold">CÁLCULO PRÉVIO DO IMC</p>
                        <h2 class="text-success fw-bold mb-1" id="valor-imc">0.00</h2>
                        <span class="badge fs-6 px-3 py-2 rounded-pill" id="classificacao-imc">Analisando...</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Anotações / Conduta Clínica</label>
                    <textarea name="anotacoes" class="form-control" rows="4" placeholder="Digite observações da evolução..."></textarea>
                </div>

                <hr class="text-muted mb-4">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="prontuario.php?id=<?php echo $paciente_id; ?>" class="btn btn-outline-secondary px-4">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Salvar Avaliação
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
const inputPeso = document.getElementById('peso');
const inputAltura = document.getElementById('altura');
const painelImc = document.getElementById('painel-imc');
const valorImc = document.getElementById('valor-imc');
const classifImc = document.getElementById('classificacao-imc');

function calcularIMCLive() {
    const peso = parseFloat(inputPeso.value);
    const altura = parseFloat(inputAltura.value);

    if (peso > 0 && altura > 0) {
        const imc = (peso / (altura * altura)).toFixed(2);
        painelImc.classList.remove('d-none');
        valorImc.innerText = imc.replace('.', ',');

        if (imc < 18.5) {
            classifImc.innerText = "Abaixo do peso";
            classifImc.className = "badge bg-warning text-dark";
        } else if (imc >= 18.5 && imc <= 24.99) {
            classifImc.innerText = "Peso normal (Eutrofia)";
            classifImc.className = "badge bg-success";
        } else if (imc >= 25 && imc <= 29.99) {
            classifImc.innerText = "Sobrepeso";
            classifImc.className = "badge bg-warning text-dark";
        } else if (imc >= 30 && imc <= 34.99) {
            classifImc.innerText = "Obesidade Grau I";
            classifImc.className = "badge bg-danger bg-opacity-75 text-white";
        } else if (imc >= 35 && imc <= 39.99) {
            classifImc.innerText = "Obesidade Grau II (Moderada)";
            classifImc.className = "badge bg-danger text-white fw-bold";
        } else {
            classifImc.innerText = "Obesidade Grau III (Grave)";
            classifImc.className = "badge bg-dark text-white fw-bold";
        }
    } else {
        painelImc.classList.add('d-none');
    }
}

inputPeso.addEventListener('input', calcularIMCLive);
inputAltura.addEventListener('input', calcularIMCLive);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
