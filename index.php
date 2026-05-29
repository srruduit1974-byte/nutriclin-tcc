<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
require 'conexao.php'; // ajuste para seu arquivo de conexão
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Nutriclin - Login/Cadastro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #00695c, #004d40); /* ajuste conforme cores do TCC */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .login-section {
      background-color: #004d40; /* cor escura */
      color: #fff;
      padding: 40px;
    }
    .signup-section {
      background-color: #fff; /* cor clara */
      padding: 40px;
    }
    .btn-custom {
      background-color: #00695c;
      color: #fff;
    }
    .btn-custom:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card d-flex flex-row">
          
          <!-- LOGIN -->
          <div class="col-md-6 login-section d-flex flex-column justify-content-center">
            <h2 class="mb-4">Bem-vindo de volta!</h2>
            <form action="login.php" method="POST">
              <div class="mb-3">
                <label for="emailLogin" class="form-label">Email</label>
                <input type="email" class="form-control" id="emailLogin" name="email" required>
              </div>
              <div class="mb-3">
                <label for="senhaLogin" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senhaLogin" name="senha" required>
              </div>
              <button type="submit" class="btn btn-custom w-100">Entrar</button>
            </form>
          </div>
          
          <!-- CADASTRO -->
          <form action="cadastro_usuario.php" method="POST">
          <div class="mb-3">
  <label for="tipo" class="form-label">Tipo de Usuário</label>
  <select class="form-select" id="tipo" name="tipo" required>
    <option value="nutricionista">Nutricionista</option>
    <option value="estagiario">Estagiário</option>
  </select>
</div>
<div class="mb-3">
  <label for="extra" id="extraLabel" class="form-label">CRN</label>
  <input type="text" class="form-control" id="extra" name="extra" required>
</div>

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
  <div class="mb-3">
    <label for="nome" class="form-label">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" required>
  </div>
  
  <div class="mb-3">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="text" class="form-control" id="telefone" name="telefone" required>
  </div>
  <div class="mb-3">
    <label for="emailCadastro" class="form-label">Email</label>
    <input type="email" class="form-control" id="emailCadastro" name="email" required>
  </div>
  <div class="mb-3">
    <label for="senhaCadastro" class="form-label">Senha</label>
    <input type="password" class="form-control" id="senhaCadastro" name="senha" required>
  </div>
  <button type="submit" class="btn btn-custom w-100">Registrar</button>
</form>

          </div>
          
        </div>
      </div>
    </div>
  </div>
</body>
</html>
