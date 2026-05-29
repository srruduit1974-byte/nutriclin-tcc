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

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_SANITIZE_NUMBER_INT);
$peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
$altura = filter_input(INPUT_POST, 'altura', FILTER_VALIDATE_FLOAT);
$anotacoes = $_POST['anotacoes'] ?? "";

if (!$paciente_id) {
    die("Paciente não informado.");
}

if (!$peso || !$altura) {
    die("Peso ou altura inválidos.");
}

$sql_paciente = "SELECT id FROM pacientes 
                 WHERE id = ? 
                 AND nutricionista_id = ?";

$stmt_paciente = $conn->prepare($sql_paciente);
$stmt_paciente->bind_param("ii", $paciente_id, $nutricionista_id);
$stmt_paciente->execute();

$resultado = $stmt_paciente->get_result();

if ($resultado->num_rows === 0) {
    die("Paciente não encontrado ou acesso negado.");
}

$imc = $peso / ($altura * $altura);

$sql = "INSERT INTO consultas 
        (paciente_id, nutricionista_id, peso, altura, imc, anotacoes)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iiddds",
    $paciente_id,
    $nutricionista_id,
    $peso,
    $altura,
    $imc,
    $anotacoes
);

if ($stmt->execute()) {
    header("Location: prontuario.php?id=" . $paciente_id);
    exit();
}

die("Erro ao salvar avaliação.");
?>
