<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado e é um profissional
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'profissional') {
    header('Location: ../public/home.php');
    exit;
}

// Obtém o ID do profissional logado
$profissional_id = $_SESSION['usuario']['id'];

// Busca os agendamentos para este profissional
$stmt = $pdo->prepare("
    SELECT 
        a.id as agendamento_id,
        u.nome as cliente_nome,
        s.nome as servico_nome,
        a.data_hora_inicio,
        a.status
    FROM agendamentos a
    JOIN usuarios u ON a.cliente_id = u.id
    JOIN servicos s ON a.servico_id = s.id
    WHERE a.profissional_id = ?
    ORDER BY a.data_hora_inicio ASC
");
$stmt->execute([$profissional_id]);
$agendamentos = $stmt->fetchAll();

// Processa o formulário de indisponibilidade se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['motivo'])) {
    $motivo = $_POST['motivo'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO indisponibilidades 
            (profissional_id, data_hora_inicio, data_hora_fim, motivo) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$profissional_id, $data_inicio, $data_fim, $motivo]);
        
        $_SESSION['mensagem'] = "Período de indisponibilidade registrado com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao registrar indisponibilidade: " . $e->getMessage();
    }
    
    header('Location: telabarbeiro.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agenda de Clientes</title>
  <link rel="stylesheet" href="../assets/css/styleTelaBarbeiro.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <style>
    /* Estilos adicionais para melhorar a visualização */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .table th, .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .table th {
        background-color: #f2f2f2;
    }
    .status-pendente {
        color: #FFA500;
    }
    .status-confirmado {
        color: #008000;
    }
    .status-cancelado {
        color: #FF0000;
    }
    .ausencia {
        margin-top: 40px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    .ausencia .form-group {
        margin-bottom: 15px;
    }
    .ausencia label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .ausencia input[type="datetime-local"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .ausencia textarea {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
        min-height: 100px;
    }
    .ausencia button {
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #FFB22C;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .mensagem {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    .mensagem.sucesso {
        background-color: #d4edda;
        color: #155724;
    }
    .mensagem.erro {
        background-color: #f8d7da;
        color: #721c24;
    }

    body > header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 80px;
        background-color: #FFB22C;
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
    }

    body > header .logo {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    body > header .logo img {
        height: 40px;
        width: auto;
    }

    body > header nav ul {
        display: flex;
        list-style: none;
        gap: 25px;
        margin: 0;
        padding: 0;
    }

    body > header nav ul li {
        margin: 0;
    }

    body > header nav ul li a {
        text-decoration: none;
        color: #333;
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.3s;
        padding: 5px 0;
        position: relative;
    }

    body > header nav ul li a:hover {
        color: #fff;
    }

    body > header nav ul li a:after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background: #fff;
        bottom: 0;
        left: 0;
        transition: width 0.3s;
    }

    body > header nav ul li a:hover:after {
        width: 100%;
    }

    /* DESTAQUE PARA O LINK ATIVO */
    body > header nav ul li a[href="telabarbeiro.php"] {
        color: #fff;
        font-weight: 600;
    }

    body > header nav ul li a[href="telabarbeiro.php"]:after {
        width: 100%;
    }

    /* RESPONSIVIDADE */
    @media (max-width: 768px) {
        body > header {
            padding: 15px 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        body > header nav ul {
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
    }
  </style>
</head>
<body>
  <header>
      <div class="logo">
          <img src="../assets/imgs/header/Cortai.png" alt="Cortai">      
          <h1>Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></h1>
      </div>
      <nav>
        <ul>
          <li><a href="telabarbeiro.php">Agenda de clientes</a></li>
          <li><a href="#ausencia">Declarar Indisponibilidade</a></li>
          <li><a href="logout.php">Sair</a></li>
        </ul>
      </nav>
  </header>

  <main>
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="mensagem <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'erro' : 'sucesso' ?>">
            <?= htmlspecialchars($_SESSION['mensagem']) ?>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <section class="agenda">
      <h2>Agenda de Clientes</h2>
      
      <?php if (empty($agendamentos)): ?>
        <p>Nenhum agendamento encontrado.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Serviço</th>
              <th>Data e Hora</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($agendamentos as $agendamento): ?>
              <tr>
                <td><?= htmlspecialchars($agendamento['cliente_nome']) ?></td>
                <td><?= htmlspecialchars($agendamento['servico_nome']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($agendamento['data_hora_inicio'])) ?></td>
                <td class="status-<?= strtolower($agendamento['status']) ?>">
                  <?= htmlspecialchars($agendamento['status']) ?>
                </td>
                <td>
                  <form method="POST" action="atualizar_status.php" style="display: inline;">
                    <input type="hidden" name="agendamento_id" value="<?= $agendamento['agendamento_id'] ?>">
                    <button type="submit" name="acao" value="confirmar" class="btn-confirmar">Confirmar</button>
                    <button type="submit" name="acao" value="cancelar" class="btn-cancelar">Cancelar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <section class="ausencia" id="ausencia">
      <h2>Declarar Indisponibilidade</h2>
      <form method="POST">
        <div class="form-group">
          <label for="data_inicio">Data/Hora Início:</label>
          <input type="datetime-local" id="data_inicio" name="data_inicio" required>
        </div>
        <div class="form-group">
          <label for="data_fim">Data/Hora Fim:</label>
          <input type="datetime-local" id="data_fim" name="data_fim" required>
        </div>
        <div class="form-group">
          <label for="motivo">Motivo:</label>
          <textarea id="motivo" name="motivo" placeholder="Digite aqui o motivo da indisponibilidade..." required></textarea>
        </div>
        <button type="submit">Registrar Indisponibilidade</button>
      </form>
    </section>
  </main>
</body>
</html>