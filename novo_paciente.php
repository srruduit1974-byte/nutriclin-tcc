<?php
session_start();
require 'config.php'; // Inclui as configurações se necessário
require 'conexao.php';

// Proteção da página: se não estiver logado, joga para o index (login)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $sexo = $_POST['sexo'] ?? 'OUTRO';
    $aceite_lgpd = isset($_POST['aceite_lgpd']) ? 1 : 0;
    $data_aceite = $aceite_lgpd ? date("Y-m-d H:i:s") : null;

    // 🔒 Validação obrigatória LGPD
    if ($aceite_lgpd !== 1) {
        $mensagem = "<div class='alert alert-danger'>É obrigatório aceitar o termo LGPD para cadastrar o paciente.</div>";
    } else {
        $nutricionista_id = $_SESSION['nutricionista_id'] ?? null;

        $sql = "INSERT INTO pacientes (nome, cpf, telefone, sexo, aceite_lgpd, data_aceite, nutricionista_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisi", $nome, $cpf, $telefone, $sexo, $aceite_lgpd, $data_aceite, $nutricionista_id);

        if ($stmt->execute()) {
            $stmt->close();
            // Redireciona direto para a página de pacientes após cadastrar
            header("Location: pacientes.php");
            exit();
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: {$stmt->error}</div>";
            $stmt->close();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Paciente - NutriClin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="text-success mb-4 fw-bold">Cadastrar Novo Paciente</h2>
    <?php echo $mensagem; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome completo</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Sexo</label>
            <select name="sexo" class="form-select">
                <option value="OUTRO">Outro</option>
                <option value="F">Feminino</option>
                <option value="M">Masculino</option>
            </select>
        </div>

        <!-- LGPD -->
        <div class="form-check mb-3">
            <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#lgpdModal">
                Ler termo de consentimento LGPD
            </button>
            <input type="checkbox" name="aceite_lgpd" id="aceite_lgpd" class="form-check-input" required>
            <label for="aceite_lgpd" class="form-check-label">
                Li e aceito o uso dos dados conforme LGPD
            </label>
        </div>

        <!-- Modal LGPD -->
        <div class="modal fade" id="lgpdModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Termo de Consentimento - LGPD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body" style="max-height:300px; overflow-y:auto;">
                <p>Este sistema segue a Lei Geral de Proteção de Dados (LGPD)...</p>
                <p>O paciente autoriza o uso de seus dados sensíveis exclusivamente para fins de atendimento nutricional...</p>
                <p>O consentimento é indispensável para acesso ao prontuário e realização de avaliações clínicas.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnLi">Confirmar Leitura</button>
              </div>
            </div>
          </div>
        </div>

        <script>
          const btnLi = document.getElementById('btnLi');
          const checkbox = document.getElementById('aceite_lgpd');
          btnLi.addEventListener('click', function() {
            checkbox.disabled = false;
            alert("Agora você pode marcar o aceite LGPD.");
          });
        </script>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="pacientes.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'rodape.php'; ?>

</body>
</html>
