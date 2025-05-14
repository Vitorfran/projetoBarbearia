<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home.php');
    exit;
}

// Buscar profissionais
$profissionais = $pdo->query("SELECT * FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

// Buscar serviços
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll();

// Verifica mensagens de erro/sucesso
$erro = $_SESSION['erro'] ?? '';
$sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['erro'], $_SESSION['sucesso']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendamento</title>
  <link rel="stylesheet" href="../assets/css/agendamento.css">
</head>
<body>
  <div class="container">
    <h2>Agendamento</h2>
    
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form action="processa_agendamento.php" method="POST" class="form">
      
      <!-- Lista de serviços -->
      <div class="card-servico">
        <div class="cabecalho-card">
          <label for="servico">Serviço:</label>
          <select name="servico_id" required>
            <?php foreach ($servicos as $servico): ?>
              <option value="<?= $servico['id'] ?>">
                <?= htmlspecialchars($servico['nome']) ?> - R$<?= number_format($servico['preco'], 2, ',', '.') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Campo de data e hora do agendamento -->
      <input type="date" name="data" min="<?= date('Y-m-d') ?>" required />
      <input type="time" name="hora" min="08:00" max="20:00" required />

      <!-- Escolha do barbeiro -->
      <div class="barbeiros-container">
        <p class="titulo-barbeiro">Escolha o barbeiro:</p>
        <div class="barbeiros">
          <?php foreach ($profissionais as $prof): ?>
            <label>
              <input type="radio" name="barbeiro_id" value="<?= $prof['id'] ?>" required>
              <span><?= htmlspecialchars($prof['nome']) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="button-container">
        <button type="submit" class="submit-btn">Confirmar</button>
      </div>
    </form>
  </div>
</body>
</html>