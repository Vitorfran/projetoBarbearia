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
$horarios_indisponiveis = [];

// Buscar MEUS agendamentos para exibir
$meus_agendamentos = [];
if (isset($_SESSION['usuario']['id'])) {
    $stmt = $pdo->prepare("
        SELECT a.*, s.nome as servico_nome, u.nome as profissional_nome 
        FROM agendamentos a
        JOIN servicos s ON a.servico_id = s.id
        JOIN usuarios u ON a.profissional_id = u.id
        WHERE a.cliente_id = ?
        ORDER BY a.data_hora_inicio DESC
    ");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $meus_agendamentos = $stmt->fetchAll();
}

if (!empty($data_agendamento) && !empty($hora_agendamento)) {
    $data_hora_inicio = $data_agendamento . ' ' . $hora_agendamento . ':00';
    $data_hora_fim = date('Y-m-d H:i:s', strtotime($data_hora_inicio . ' +1 hour'));
    
    // Buscar horários já agendados para mostrar ao usuário
    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(data_hora_inicio, '%H:%i') as hora_agendada 
        FROM agendamentos 
        WHERE DATE(data_hora_inicio) = ? 
        AND status != 'cancelado'
        ORDER BY hora_agendada
    ");
    $stmt->execute([$data_agendamento]);
    $horarios_agendados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $horarios_indisponiveis = $horarios_agendados;
    
    // Buscar profissionais disponíveis considerando agendamentos e indisponibilidades
    $stmt = $pdo->prepare("
        SELECT u.* 
        FROM usuarios u
        WHERE u.tipo = 'profissional'
        AND NOT EXISTS (
            SELECT 1 
            FROM indisponibilidades i
            WHERE i.profissional_id = u.id
            AND (
                (? BETWEEN i.data_hora_inicio AND i.data_hora_fim)
                OR (? BETWEEN i.data_hora_inicio AND i.data_hora_fim)
                OR (i.data_hora_inicio BETWEEN ? AND ?)
            )
        )
        AND NOT EXISTS (
            SELECT 1 
            FROM agendamentos a
            WHERE a.profissional_id = u.id
            AND (
                (? BETWEEN a.data_hora_inicio AND a.data_hora_fim)
                OR (? BETWEEN a.data_hora_inicio AND a.data_hora_fim)
                OR (a.data_hora_inicio BETWEEN ? AND ?)
            )
            AND a.status != 'cancelado'
        )
    ");
    $stmt->execute([
        $data_hora_inicio, $data_hora_fim, $data_hora_inicio, $data_hora_fim,
        $data_hora_inicio, $data_hora_fim, $data_hora_inicio, $data_hora_fim
    ]);
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
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }
    
    .form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 30px;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    
    .submit-btn:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
    
    /* Novos estilos para horários indisponíveis */
    .horarios-indisponiveis {
      margin-top: 10px;
      padding: 10px;
      background-color: #f8d7da;
      border-radius: 4px;
      display: none;
    }
    
    .horario-indisponivel {
      display: inline-block;
      margin-right: 5px;
      padding: 3px 8px;
      background-color: #dc3545;
      color: white;
      border-radius: 3px;
      font-size: 12px;
    }
    
    /* Estilos para a lista de agendamentos */
    .meus-agendamentos {
      margin-top: 40px;
    }
    
    .agendamentos-titulo {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #333;
      border-bottom: 2px solid #FFB22C;
      padding-bottom: 10px;
    }
    
    .agendamento-item {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    }
    
    .agendamento-data {
      color: #666;
      font-size: 0.9rem;
    }
    
    .agendamento-profissional {
      color: #555;
    }
    
    .agendamento-status {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    
    .status-agendado {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .status-concluido {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-cancelado {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .sem-agendamentos {
      text-align: center;
      color: #666;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
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
      <div>
        <label for="dataAgendamento">Data:</label>
        <input type="date" name="data" id="dataAgendamento" min="<?= date('Y-m-d') ?>" required>
      </div>
      
      <div>
        <label for="horaAgendamento">Horário:</label>
        <input type="time" name="hora" id="horaAgendamento" min="08:00" max="20:00" step="1800" required>
        <div class="horarios-indisponiveis" id="horariosIndisponiveis">
          <strong>Horários indisponíveis neste dia:</strong>
          <div id="listaHorariosIndisponiveis"></div>
        </div>
      </div>

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
        <div id="semBarbeiros" style="display: none; color: #dc3545; margin-top: 10px;">
          Nenhum barbeiro disponível para o horário selecionado.
        </div>
      </div>

      <div class="button-container">
        <button type="submit" class="submit-btn" id="submitBtn">Confirmar Agendamento</button>
      </div>
    </form>

    <!-- Seção para mostrar MEUS agendamentos -->
    <div class="meus-agendamentos">
      <h3 class="agendamentos-titulo">Meus Agendamentos</h3>
      
      <?php if (count($meus_agendamentos) > 0): ?>
        <?php foreach ($meus_agendamentos as $agendamento): ?>
          <div class="agendamento-item">
            <div class="agendamento-header">
              <span class="agendamento-servico"><?= htmlspecialchars($agendamento['servico_nome']) ?></span>
              <span class="agendamento-data">
                <?= date('d/m/Y H:i', strtotime($agendamento['data_hora_inicio'])) ?>
              </span>
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
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="sem-agendamentos">
          <p>Você ainda não possui agendamentos.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dataInput = document.getElementById('dataAgendamento');
      const horaInput = document.getElementById('horaAgendamento');
      const barbeirosContainer = document.getElementById('barbeirosContainer');
      const semBarbeirosMsg = document.getElementById('semBarbeiros');
      const horariosIndisponiveis = document.getElementById('horariosIndisponiveis');
      const listaHorariosIndisponiveis = document.getElementById('listaHorariosIndisponiveis');
      const submitBtn = document.getElementById('submitBtn');
      
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
          .then(data => {
            barbeirosContainer.innerHTML = '';
            semBarbeirosMsg.style.display = 'none';
            submitBtn.disabled = false;
            
            if (data.profissionais.length === 0) {
              semBarbeirosMsg.style.display = 'block';
              submitBtn.disabled = true;
              return;
            }
            
            data.profissionais.forEach(prof => {
              const label = document.createElement('label');
              label.innerHTML = `
                <input type="radio" name="barbeiro_id" value="${prof.id}" required>
                <span>${prof.nome}</span>
              `;
              barbeirosContainer.appendChild(label);
            });
            
            // Mostrar horários indisponíveis
            if (data.horarios_indisponiveis && data.horarios_indisponiveis.length > 0) {
              horariosIndisponiveis.style.display = 'block';
              listaHorariosIndisponiveis.innerHTML = '';
              
              data.horarios_indisponiveis.forEach(horario => {
                const span = document.createElement('span');
                span.className = 'horario-indisponivel';
                span.textContent = horario;
                listaHorariosIndisponiveis.appendChild(span);
              });
            } else {
              horariosIndisponiveis.style.display = 'none';
            }
          })
          .catch(error => {
            console.error('Erro ao buscar barbeiros:', error);
          });
        }
      }
      
      dataInput.addEventListener('change', function() {
        // Quando a data muda, buscar horários indisponíveis para essa data
        if (dataInput.value) {
          fetch('busca_horarios_indisponiveis.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `data=${encodeURIComponent(dataInput.value)}`
          })
          .then(response => response.json())
          .then(horarios => {
            if (horarios.length > 0) {
              horariosIndisponiveis.style.display = 'block';
              listaHorariosIndisponiveis.innerHTML = '';
              
              horarios.forEach(horario => {
                const span = document.createElement('span');
                span.className = 'horario-indisponivel';
                span.textContent = horario;
                listaHorariosIndisponiveis.appendChild(span);
              });
            } else {
              horariosIndisponiveis.style.display = 'none';
            }
          })
          .catch(error => {
            console.error('Erro ao buscar horários indisponíveis:', error);
          });
        }
        
        atualizarBarbeirosDisponiveis();
      });
      
      horaInput.addEventListener('change', atualizarBarbeirosDisponiveis);
    });
  </script>
</body>
</html>