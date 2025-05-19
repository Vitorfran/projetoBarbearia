<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home.php');
    exit;
}

// Buscar serviços
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll();

// Buscar profissionais disponíveis
$profissionais_disponiveis = [];
$data_agendamento = $_POST['data'] ?? '';
$hora_agendamento = $_POST['hora'] ?? '';

if (!empty($data_agendamento) && !empty($hora_agendamento)) {
    $data_hora_agendamento = $data_agendamento . ' ' . $hora_agendamento . ':00';
    
    $stmt = $pdo->prepare("
        SELECT u.* 
        FROM usuarios u
        WHERE u.tipo = 'profissional'
        AND NOT EXISTS (
            SELECT 1 
            FROM indisponibilidades i
            WHERE i.profissional_id = u.id
            AND ? BETWEEN i.data_hora_inicio AND i.data_hora_fim
        )
    ");
    $stmt->execute([$data_hora_agendamento]);
    $profissionais_disponiveis = $stmt->fetchAll();
} else {
    $profissionais_disponiveis = $pdo->query("SELECT * FROM usuarios WHERE tipo = 'profissional'")->fetchAll();
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
  <title>Agendamento</title>
  <link rel="stylesheet" href="../assets/css/agendamento.css">
  <style>
    .container {
      max-width: 800px;
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
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }
    
    .form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    
    .card-servico {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .cabecalho-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    
    input[type="date"],
    input[type="time"] {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }
    
    .barbeiros-container {
      margin-top: 20px;
    }
    
    .titulo-barbeiro {
      font-weight: bold;
      margin-bottom: 10px;
    }
    
    .barbeiros {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 10px;
    }
    
    .barbeiros label {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .barbeiros label:hover {
      background-color: #e9ecef;
    }
    
    .button-container {
      margin-top: 20px;
      text-align: center;
    }
    
    .submit-btn {
      padding: 12px 24px;
      background-color: #FFB22C;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
      background-color: #e69c00;
    }
  </style>
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

    <form action="processa_agendamento.php" method="POST" class="form" id="formAgendamento">
      
      <!-- Lista de serviços -->
      <div class="card-servico">
        <div class="cabecalho-card">
          <label for="servico">Serviço:</label>
          <select name="servico_id" style="margin:8px; border-radius:5px; background-color: #ffd074; border: none;" required>
            <?php foreach ($servicos as $servico): ?>
              <option value="<?= $servico['id'] ?>">
                <?= htmlspecialchars($servico['nome']) ?> - R$<?= number_format($servico['preco'], 2, ',', '.') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Campo de data e hora do agendamento -->
      <input type="date" name="data" id="dataAgendamento" min="<?= date('Y-m-d') ?>" required>
      <input type="time" name="hora" id="horaAgendamento" min="08:00" max="20:00" required>

      <!-- Escolha do barbeiro -->
      <div class="barbeiros-container">
        <p class="titulo-barbeiro">Escolha o barbeiro:</p>
        <div class="barbeiros" id="barbeirosContainer">
          <?php foreach ($profissionais_disponiveis as $prof): ?>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dataInput = document.getElementById('dataAgendamento');
      const horaInput = document.getElementById('horaAgendamento');
      const barbeirosContainer = document.getElementById('barbeirosContainer');
      
      function atualizarBarbeirosDisponiveis() {
        const data = dataInput.value;
        const hora = horaInput.value;
        
        if (data && hora) {
          fetch('busca_barbeiros.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `data=${encodeURIComponent(data)}&hora=${encodeURIComponent(hora)}`
          })
          .then(response => response.json())
          .then(profissionais => {
            barbeirosContainer.innerHTML = '';
            
            if (profissionais.length === 0) {
              barbeirosContainer.innerHTML = '<p>Nenhum barbeiro disponível neste horário</p>';
              return;
            }
            
            profissionais.forEach(prof => {
              const label = document.createElement('label');
              label.innerHTML = `
                <input type="radio" name="barbeiro_id" value="${prof.id}" required>
                <span>${prof.nome}</span>
              `;
              barbeirosContainer.appendChild(label);
            });
          })
          .catch(error => {
            console.error('Erro ao buscar barbeiros:', error);
          });
        }
      }
      
      dataInput.addEventListener('change', atualizarBarbeirosDisponiveis);
      horaInput.addEventListener('change', atualizarBarbeirosDisponiveis);
    });
  </script>
</body>
</html>