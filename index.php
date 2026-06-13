<?php
set_time_limit(15);
ini_set('memory_limit', '256M');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nutriclin - Login</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
  <style>
    body { background: linear-gradient(135deg, #00695c, #004d40); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .card-login { background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); padding: 40px; max-width: 420px; width: 100%; }
    .btn-submit { background-color: #00695c; color: #fff; font-weight: 600; }
    .btn-submit:hover { background-color: #004d40; color: #fff; }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center">
    <div class="card-login">
      <div class="text-center mb-4">
        <div class="display-5 text-success mb-2"><i class="bi bi-heart-pulse-fill text-success"></i></div>
        <h2 class="fw-bold text-dark mb-1">NutriClin</h2>
        <p class="text-muted small">Gestão Nutricional Inteligente</p>
      </div>

      <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger text-center py-2">Credenciais inválidas. Tente novamente.</div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="mb-3">
          <label for="emailLogin" class="form-label fw-semibold text-secondary">Endereço de E-mail</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="emailLogin" name="email" placeholder="nome@exemplo.com" required>
          </div>
        </div>
        <div class="mb-4">
          <label for="senhaLogin" class="form-label fw-semibold text-secondary">Senha de Acesso</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="senhaLogin" name="senha" placeholder="Digite sua senha" required>
          </div>
        </div>
        <button type="submit" class="btn btn-submit w-100 py-2 shadow-sm">Entrar no Sistema</button>
      </form>

      <div class="text-center mt-4 pt-2 border-top border-light">
        <p class="mb-0 text-muted small">Novo profissional? <a href="registro.php" class="text-success fw-bold text-decoration-none">Criar uma conta</a></p>
      </div>
    </div>
  </div>
</body>
</html>
