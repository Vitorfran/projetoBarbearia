<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header('Location: ../public/home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_SESSION['usuario']['id'];
    $profissional_id = $_POST['profissional_id'] ?? null;
    $servico_id = $_POST['servico_id'] ?? null;
    $data = $_POST['data'] ?? null;
    $hora = $_POST['hora'] ?? null;

    if (empty($cliente_id) || empty($profissional_id) || empty($servico_id) || empty($data) || empty($hora)) {
        $_SESSION['erro'] = "Erro: Todos os campos são obrigatórios.";
        header('Location: agendamento.php');
        exit;
    }

    $data_hora_inicio_str = $data . ' ' . $hora . ':00';
    $timezone = new DateTimeZone('America/Sao_Paulo');
    $inicio = new DateTime($data_hora_inicio_str, $timezone);
    $agora = new DateTime('now', $timezone);

    if ($inicio < $agora) {
        $_SESSION['erro'] = "Você não pode agendar para uma data ou hora no passado.";
        header('Location: agendamento.php');
        exit;
    }

    $stmtDuracao = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
    $stmtDuracao->execute([$servico_id]);
    $duracao = $stmtDuracao->fetchColumn();

    if (!$duracao) {
        $_SESSION['erro'] = "Serviço inválido ou não encontrado.";
        header('Location: agendamento.php');
        exit;
    }

    $fim = clone $inicio;
    $fim->modify("+{$duracao} minutes");
    $data_hora_inicio_sql = $inicio->format("Y-m-d H:i:s");
    $data_hora_fim_sql = $fim->format("Y-m-d H:i:s");

    try {
        $stmtIdentico = $pdo->prepare("
            SELECT COUNT(*) FROM agendamentos 
            WHERE profissional_id = ? 
            AND servico_id = ? 
            AND data_hora_inicio = ? 
            AND status NOT IN ('cancelado', 'rejeitado')
        ");
        $stmtIdentico->execute([$profissional_id, $servico_id, $data_hora_inicio_sql]);
        
        if ($stmtIdentico->fetchColumn() > 0) {
            $_SESSION['erro'] = "Já existe um agendamento idêntico.";
            header('Location: agendamento.php');
            exit;
        }

        $stmtCliente = $pdo->prepare("
            SELECT COUNT(*) FROM agendamentos
            WHERE cliente_id = ? 
            AND status NOT IN ('cancelado', 'rejeitado')
            AND (
                (data_hora_inicio <= ? AND data_hora_fim > ?) OR
                (data_hora_inicio < ? AND data_hora_fim >= ?) OR
                (data_hora_inicio >= ? AND data_hora_fim <= ?)
            )
        ");
        $stmtCliente->execute([
            $cliente_id, 
            $data_hora_inicio_sql, $data_hora_inicio_sql,
            $data_hora_fim_sql, $data_hora_fim_sql,
            $data_hora_inicio_sql, $data_hora_fim_sql
        ]);
        
        if ($stmtCliente->fetchColumn() > 0) {
            $_SESSION['erro'] = "Você já possui outro agendamento no mesmo horário";
            header('Location: agendamento.php');
            exit;
        }

        $stmtProf = $pdo->prepare("
            SELECT COUNT(*) FROM agendamentos
            WHERE profissional_id = ? 
            AND status NOT IN ('cancelado', 'rejeitado')
            AND (
                (data_hora_inicio <= ? AND data_hora_fim > ?) OR
                (data_hora_inicio < ? AND data_hora_fim >= ?) OR
                (data_hora_inicio >= ? AND data_hora_fim <= ?)
            )
        ");
        $stmtProf->execute([
            $profissional_id,
            $data_hora_inicio_sql, $data_hora_inicio_sql,
            $data_hora_fim_sql, $data_hora_fim_sql,
            $data_hora_inicio_sql, $data_hora_fim_sql
        ]);
        
        if ($stmtProf->fetchColumn() > 0) {
            $_SESSION['erro'] = "O profissional selecionado já possui um atendimento neste horário.";
            header('Location: agendamento.php');
            exit;
        }

        $sql_insert = "
            INSERT INTO agendamentos (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim, status)
            VALUES (?, ?, ?, ?, ?, 'pendente')
        ";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$cliente_id, $profissional_id, $servico_id, $data_hora_inicio_sql, $data_hora_fim_sql]);

        $_SESSION['sucesso'] = "Seu serviço foi agendado!";
        header('Location: agendamento.php');
        exit;

    } catch (PDOException $e) {
        error_log("Erro no agendamento: " . $e->getMessage());
        $_SESSION['erro'] = "Ocorreu um erro ao processar o agendamento. Tente novamente.";
        header('Location: agendamento.php');
        exit;
    }
}

$servicos = $pdo->query("SELECT * FROM servicos WHERE ativo = 1")->fetchAll();
$profissionais_disponiveis = $pdo->query("SELECT * FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

$erro = $_SESSION['erro'] ?? '';
$sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['erro'], $_SESSION['sucesso']);
?>

<?php include '../partes/header.php'; ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendamento</title>
  <link rel="stylesheet" href="../assets/css/agendamento.css">
</head>
<body>
  <?php require_once '../partes/header.php'; ?>
  <div class="container">
    <h2>Agendamento</h2>
    
    <?php if ($erro): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form action="agendamento.php" method="POST" class="form" id="formAgendamento">
      
      <div class="card-servico">
        <div class="cabecalho-card">
          <label for="servico">Serviço:</label>
          <select name="servico_id" style="margin:12px; border-radius:5px; background-color: #ffd074; border: none;" required>
            <?php foreach ($servicos as $servico): ?>
              <option value="<?= $servico['id'] ?>">
                <?= htmlspecialchars($servico['nome']) ?> - R$<?= number_format($servico['preco'], 2, ',', '.') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <input type="date" name="data" id="dataAgendamento" min="<?= date('Y-m-d') ?>" required>
      <input type="time" name="hora" id="horaAgendamento" min="08:00" max="20:00" required>

      <div class="barbeiros-container">
        <p class="titulo-barbeiro">Escolha o barbeiro:</p>
        <div class="barbeiros" id="barbeirosContainer">
          <?php foreach ($profissionais_disponiveis as $prof): ?>
            <label>
              <input type="radio" name="profissional_id" value="<?= $prof['id'] ?>" required>
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
                <input type="radio" name="profissional_id" value="${prof.id}" required>
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
  <?php require_once '../partes/footer.php'; ?>
</body>
</html>
