<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';
require 'config.php';

// Se não estiver logado, volta para login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $observacoes = $_POST['observacoes'];

    $dataHora = $data . ' ' . $hora;

    // 1. Busca o ID da nutricionista baseado no usuário logado na sessão
    $sqlNutri = "SELECT id FROM nutricionistas WHERE usuario_id = ?";
    $stmtNutri = $conn->prepare($sqlNutri);
    $stmtNutri->bind_param("i", $_SESSION['usuario_id']);
    $stmtNutri->execute();
    $resultNutri = $stmtNutri->get_result();
    $nutriData = $resultNutri->fetch_assoc();

    // Se não encontrar nenhuma nutricionista ligada a esse usuário logado
    if (!$nutriData) {
        die("Erro: Seu usuário não está cadastrado ou vinculado como uma Nutricionista no sistema.");
    }

    $nutricionista_id = $nutriData['id'];

    // 2. Realiza o agendamento com o ID correto da nutricionista
    $sqlInsert = "INSERT INTO agendamentos (paciente_id, nutricionista_id, data_hora, observacoes, status) 
                  VALUES (?, ?, ?, ?, 'agendada')";
    $stmt = $conn->prepare($sqlInsert);
    
    // Agora passamos a variável $nutricionista_id correta aqui
    $stmt->bind_param("iiss", $paciente_id, $nutricionista_id, $dataHora, $observacoes);

    if (!$stmt->execute()) {
        die("Erro ao agendar: " . $stmt->error);
    }
    
    // Redireciona para recarregar a página com sucesso e evitar reenvio de dados ao dar F5
    header("Location: agendamentos.php?sucesso=1");
    exit();
}

// --- BUSCA PACIENTES PARA MANDAR PRO SELECT DO FORMULÁRIO ---
$sqlPacientes = "SELECT id, nome FROM pacientes";
$resultPacientes = $conn->query($sqlPacientes);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema Nutriclin - Agendamentos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f7f6; }
        .container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #2ecc71; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #27ae60; }
        .alerta-sucesso { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center; }
        .nav-link { display: block; text-align: center; margin-top: 15px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Novo Agendamento</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alerta-sucesso">Agendamento realizado com sucesso!</div>
    <?php endif; ?>

    <form action="agendamentos.php" method="POST">
        <div class="form-group">
            <label for="paciente_id">Paciente:</label>
            <select name="paciente_id" id="paciente_id" required>
                <option value="">Selecione um paciente...</option>
                <?php while($paciente = $resultPacientes->fetch_assoc()): ?>
                    <option value="<?php echo $paciente['id']; ?>"><?php echo htmlspecialchars($paciente['nome']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="data">Data:</label>
            <input type="date" name="data" id="data" required>
        </div>

        <div class="form-group">
            <label for="hora">Horário:</label>
            <input type="time" name="hora" id="hora" required>
        </div>

        <div class="form-group">
            <label for="observacoes">Observações (Opcional):</label>
            <textarea name="observacoes" id="observacoes" rows="4"></textarea>
        </div>

        <button type="submit">Confirmar Agendamento</button>
    </form>
    
    <a href="dashboard.php" class="nav-link">Voltar ao Painel</a>
</div>

</body>
</html>
