<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); 
    exit();
}

$tipo = $_SESSION['tipo'] ?? 'desconhecido';
$nome = $_SESSION['user_nome'] ?? $_SESSION['email_user'] ?? 'Usuário'; 

// Captura e força o padrão caso venha vazio
$periodo = (isset($_GET['periodo']) && $_GET['periodo'] !== '') ? $_GET['periodo'] : 'diario';
$data_filtro = (isset($_GET['data_filtro']) && $_GET['data_filtro'] !== '') ? $_GET['data_filtro'] : date('Y-m-d');

// Extrai pedaços da data para o filtro por texto (LIKE)
$ano = date('Y', strtotime($data_filtro));
$mes = date('m', strtotime($data_filtro));
$dia = date('d', strtotime($data_filtro));

$sql_base = "SELECT a.id, a.data_hora, a.observacoes, a.status, p.nome AS nome_paciente 
             FROM agendamentos a 
             INNER JOIN pacientes p ON a.paciente_id = p.id";

// Montagem estrita da Query
if ($periodo == 'diario') {
    $data_busca = "{$ano}-{$mes}-{$dia}";
    $query = "$sql_base WHERE a.data_hora LIKE '$data_busca%' ORDER BY a.data_hora ASC";
    $titulo_relatorio = "Relatório Diário de Agendamentos - " . date('d/m/Y', strtotime($data_filtro));
} elseif ($periodo == 'mensal') {
    $data_busca = "{$ano}-{$mes}";
    $query = "$sql_base WHERE a.data_hora LIKE '$data_busca%' ORDER BY a.data_hora ASC";
    $titulo_relatorio = "Relatório Mensal de Agendamentos - " . date('m/Y', strtotime($data_filtro));
} else { 
    $data_busca = "{$ano}";
    $query = "$sql_base WHERE a.data_hora LIKE '$data_busca%' ORDER BY a.data_hora ASC";
    $titulo_relatorio = "Relatório Anual de Agendamentos - " . $ano;
}

$resultado = mysqli_query($conn, $query);
$total_agendamentos = mysqli_num_rows($resultado);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - NutriClin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .nav-bar { background-color: #198754; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; border-radius: 4px; margin-bottom: 20px; }
        .nav-bar a { color: white; text-decoration: none; font-weight: bold; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 4px; }
        .filtro-box { background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .filtro-box form { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        select, input[type="date"], button, input[type="submit"] { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn-filtrar { background-color: #198754; color: white; cursor: pointer; border: none; font-weight: bold; }
        .btn-filtrar:hover { background-color: #146c43; }
        .btn-print { background-color: #0d6efd; color: white; cursor: pointer; border: none; font-weight: bold; margin-left: auto; }
        .header-relatorio { display: flex; justify-content: space-between; border-bottom: 2px solid #198754; padding-bottom: 10px; margin-bottom: 20px; }
        .header-relatorio h2 { margin: 0; color: #198754; }
        .alert-info { background-color: #cff4fc; border-left: 5px solid #0dcaf0; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #198754; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .status-txt { font-weight: bold; text-transform: uppercase; font-size: 12px; color: #198754; }
        
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            .d-print-none, .nav-bar, .filtro-box { display: none !important; }
            th { background-color: #333 !important; color: white !important; }
        }
    </style>
</head>
<body>

    <!-- BARRA DE NAVEGAÇÃO -->
    <div class="nav-bar d-print-none">
        <div>NutriClin — Olá, <strong><?php echo htmlspecialchars($nome); ?></strong></div>
        <a href="dashboard.php">Voltar ao Painel</a>
    </div>

    <div class="container">
        
        <!-- Formulário Padrão Seguro via GET (Sem Javascript) -->
        <div class="filtro-box d-print-none">
            <h3 style="margin-top:0; color:#198754;">Filtrar Histórico</h3>
            <form method="GET" action="relatorios.php">
                
                <div class="form-group">
                    <label>Período:</label>
                    <select name="periodo">
                        <option value="diario" <?php echo $periodo == 'diario' ? 'selected' : ''; ?>>Diário</option>
                        <option value="mensal" <?php echo $periodo == 'mensal' ? 'selected' : ''; ?>>Mensal</option>
                        <option value="anual" <?php echo $periodo == 'anual' ? 'selected' : ''; ?>>Anual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Data Base:</label>
                    <input type="date" name="data_filtro" value="<?php echo $data_filtro; ?>">
                </div>

                <input type="submit" value="🔍 Buscar Consultas" class="btn-filtrar">
                <button type="button" onclick="window.print()" class="btn-print">🖨️ Imprimir Relatório</button>
                
            </form>
            
        </div>
            
        <!-- ÁREA IMPRESSA DO RELATÓRIO -->
        <div class="header-relatorio">
            <div>
                <h2>NutriClin</h2>
                <small>Clínica de Acompanhamento Nutricional</small>
            </div>
            <div style="text-align: right;">
                <strong>Documento de Controle</strong><br>
                <small>Emitido em: <?php echo date('d/m/Y H:i'); ?></small>
            </div>
        </div>

        <div class="alert-info">
            <h4 style="margin:0;"><?php echo $titulo_relatorio; ?></h4>
            <p style="margin:5px 0 0 0;">Registros encontrados: <strong><?php echo $total_agendamentos; ?></strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Horário / Data</th>
                    <th>Paciente</th>
                    <th>Observações</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($total_agendamentos > 0) {
                    while($row = mysqli_fetch_assoc($resultado)) { 
                ?>
                    <tr>
                        <td><strong><?php echo date('d/m/Y H:i', strtotime($row['data_hora'])); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['nome_paciente']); ?></td>
                        <td style="color: #666; font-size: 13px;"><?php echo htmlspecialchars($row['observacoes'] ?? 'Nenhuma'); ?></td>
                        <td class="status-txt"><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; color:#666; padding:20px;'>Nenhum registro encontrado para este período.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
<?php include 'rodape.php'; ?>

</body>
</html>
