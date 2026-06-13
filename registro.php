<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nutriclin - Cadastro</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #00695c, #004d40);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-signup {
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.25);
      padding: 40px;
      max-width: 520px;
      width: 100%;
    }
    .btn-submit {
      background-color: #00695c;
      color: #fff;
      font-weight: 600;
    }
    .btn-submit:hover {
      background-color: #004d40;
      color: #fff;
    }
  </style>
</head>
<body>

  <div class="container d-flex justify-content-center py-5">
    <div class="card-signup">
      
      <!-- Cabeçalho Visual -->
      <div class="text-center mb-4">
        <h2 class="fw-bold text-success mb-1">Criar Conta</h2>
        <p class="text-muted small">Cadastre-se para acessar o painel de controle</p>
      </div>

      <!-- Formulário -->
      <form action="<?php echo URL_BASE; ?>cadastro_usuario.php" method="POST">
        
        <div class="mb-3">
          <label for="tipo" class="form-label fw-semibold text-secondary">Tipo de Profissional</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
            <select class="form-select" id="tipo" name="tipo" required>
              <option value="nutricionista">Nutricionista</option>
              <option value="estagiario">Estagiário</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <!-- O rótulo mudará dinamicamente via JS -->
          <label for="extra" id="extraLabel" class="form-label fw-semibold text-secondary">CRN</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-card-text"></i></span>
            <input type="text" class="form-control" id="extra" name="extra" placeholder="Digite o documento de registro" required>
          </div>
        </div>

        <div class="mb-3">
          <label for="nome" class="form-label fw-semibold text-secondary">Nome Completo</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: Geraldo Silva" required>
          </div>
        </div>

        <div class="mb-3">
          <label for="telefone" class="form-label fw-semibold text-secondary">Telefone de Contato</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
            <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
          </div>
        </div>

        <div class="mb-3">
          <label for="emailCadastro" class="form-label fw-semibold text-secondary">Endereço de E-mail</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="emailCadastro" name="email" placeholder="seu-email@provedor.com" required>
          </div>
        </div>

        <div class="mb-4">
          <label for="senhaCadastro" class="form-label fw-semibold text-secondary">Senha de Acesso</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="senhaCadastro" name="senha" placeholder="Crie uma senha forte" required>
          </div>
        </div>

        <button type="submit" class="btn btn-submit w-100 py-2 shadow-sm">Registrar Profissional</button>
      </form>

      <!-- Rodapé/Voltar para o Login -->
      <div class="text-center mt-4 pt-2 border-top border-light">
        <a href="index.php" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Já possui uma conta? Entrar</a>
      </div>

    </div>
  </div>

  <!-- Script Dinâmico -->
  <script>
    document.getElementById('tipo').addEventListener('change', function() {
      const extraLabel = document.getElementById('extraLabel');
      if (this.value === 'nutricionista') {
        extraLabel.textContent = 'CRN';
      } else {
        extraLabel.textContent = 'Matrícula / Documentação do Estágio';
      }
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
