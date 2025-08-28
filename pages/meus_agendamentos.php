<?php
// meus_agendamentos.php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home.php');
    exit;
}

// Buscar MEUS agendamentos para exibir (futuros e últimos 24h)
$meus_agendamentos = [];
if (isset($_SESSION['usuario']['id'])) {
    $stmt = $pdo->prepare("
        SELECT a.*, s.nome as servico_nome, u.nome as profissional_nome 
        FROM agendamentos a
        JOIN servicos s ON a.servico_id = s.id
        JOIN usuarios u ON a.profissional_id = u.id
        WHERE a.cliente_id = ?
        AND (a.data_hora_inicio + INTERVAL 24 HOUR) > NOW()
        ORDER BY a.data_hora_inicio DESC
    ");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $meus_agendamentos = $stmt->fetchAll();
}

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
  <title>Meus Agendamentos</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }
    .alert {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .agendamentos-titulo {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #333;
      border-bottom: 2px solid #FFB22C;
      padding-bottom: 10px;
    }
    .agendamento-item {
      background-color: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-left: 4px solid #FFB22C;
    }
    .agendamento-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .agendamento-servico {
      font-weight: bold;
      color: #333;
      font-size: 1.2rem;
    }
    .agendamento-data {
      color: #666;
      font-size: 0.9rem;
      background: #f8f9fa;
      padding: 5px 10px;
      border-radius: 4px;
    }
    .agendamento-profissional {
      color: #555;
      margin-bottom: 10px;
    }
    .agendamento-status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: bold;
      margin-top: 5px;
    }
    .status-agendado {
      background-color: #fff3cd;
      color: #856404;
    }
    .status-confirmado {
      background-color: #d4edda;
      color: #155724;
    }
    .status-concluido {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    .status-cancelado {
      background-color: #f8d7da;
      color: #842029;
    }
    .sem-agendamentos {
      text-align: center;
      color: #666;
      padding: 40px 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
      border: 1px dashed #ddd;
    }
    .btn-cancelar {
      padding: 8px 16px;
      background-color: #dc3545;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
      margin-top: 10px;
    }
    .btn-cancelar:hover {
      background-color: #c82333;
    }
    .btn-cancelar:disabled {
      background-color: #6c757d;
      cursor: not-allowed;
    }
    .header {
      background-color: #1a237e;
      color: white;
      padding: 15px 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .header-container {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
    }
    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: #FFB22C;
    }
    .nav-links {
      display: flex;
      gap: 20px;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      transition: color 0.3s;
    }
    .nav-links a:hover {
      color: #FFB22C;
    }
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .user-info span {
      font-size: 0.9rem;
    }
    .logout-btn {
      background: none;
      border: 1px solid #FFB22C;
      color: #FFB22C;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .logout-btn:hover {
      background-color: #FFB22C;
      color: white;
    }
  </style>
</head>

<body>
  <!-- Header simplificado para substituir o problemático -->
  <div class="header">
    <div class="header-container">
      <div class="logo">Sistema de Agendamentos</div>
      <div class="nav-links">
        <a href="../pages/home_auth.php">Home</a>
        <a href="../public/home.php#home-secao-contato">Serviços</a>
        <a href="meus_agendamentos.php">Meus Agendamentos</a>
      </div>
      <div class="user-info">
        <span>Olá, <?php echo htmlspecialchars($_SESSION['usuario']['nome'] ?? 'Usuário'); ?></span>
        <form action="../logout.php" method="post" style="display: inline;">
          <button type="submit" class="logout-btn">Sair</button>
        </form>
      </div>
    </div>
  </div>

  <div class="container">
    <h3 class="agendamentos-titulo">Meus Agendamentos</h3>

    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if (count($meus_agendamentos) > 0): ?>
      <?php foreach ($meus_agendamentos as $agendamento): 
        $dataHora = date('d/m/Y H:i', strtotime($agendamento['data_hora_inicio']));
        $podeCancelar = (strtotime($agendamento['data_hora_inicio']) > time() && $agendamento['status'] != 'cancelado');
      ?>
        <div class="agendamento-item">
          <div class="agendamento-header">
            <span class="agendamento-servico"><?= htmlspecialchars($agendamento['servico_nome']) ?></span>
            <span class="agendamento-data"><?= $dataHora ?></span>
          </div>
          <div class="agendamento-profissional">
            Profissional: <?= htmlspecialchars($agendamento['profissional_nome']) ?>
          </div>
          <div>
            Status: 
            <span class="agendamento-status status-<?= $agendamento['status'] ?>">
              <?= ucfirst($agendamento['status']) ?>
            </span>
          </div>

          <?php if ($podeCancelar): ?>
            <form method="POST" action="cancelar_agendamento.php" onsubmit="return confirmarCancelamento();">
              <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
              <button type="submit" class="btn-cancelar">Cancelar Agendamento</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="sem-agendamentos">
        <p>Você ainda não possui agendamentos futuros ou realizados nas últimas 24 horas.</p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function confirmarCancelamento() {
      return confirm('Tem certeza que deseja cancelar este agendamento?');
    }
  </script>
</body>
</html>