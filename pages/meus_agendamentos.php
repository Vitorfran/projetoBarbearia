<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado e é um cliente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'cliente') {
    header('Location: ../public/home.php');
    exit;
}

$cliente_id = $_SESSION['usuario']['id'];

// Remove agendamentos cancelados do banco
try {
    $stmtDelete = $pdo->prepare("DELETE FROM agendamentos WHERE cliente_id = ? AND status = 'cancelado'");
    $stmtDelete->execute([$cliente_id]);
} catch (PDOException $e) {
    error_log("Erro ao deletar agendamentos cancelados: " . $e->getMessage());
}

// Processa o cancelamento antes de buscar os agendamentos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_agendamento'])) {
    $agendamento_id = $_POST['agendamento_id'];
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$agendamento_id, $cliente_id]);

    // Mensagem flash na sessão
    $_SESSION['mensagem'] = "Agendamento cancelado com sucesso!";

    // Redireciona de volta para limpar o POST e evitar reenvio do formulário
    header("Location: meus_agendamentos.php");
    exit;
}

// Busca os agendamentos
$stmt = $pdo->prepare("
    SELECT 
        a.id as agendamento_id,
        u.nome as profissional_nome,
        s.nome as servico_nome,
        a.data_hora_inicio,
        a.status
    FROM agendamentos a
    JOIN usuarios u ON a.profissional_id = u.id
    JOIN servicos s ON a.servico_id = s.id
    WHERE a.cliente_id = ?
    ORDER BY a.data_hora_inicio ASC
");
$stmt->execute([$cliente_id]);
$agendamentos = $stmt->fetchAll();

include '../partes/header.php';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meus Agendamentos</title>
  <link rel="stylesheet" href="../assets/css/styleTelaBarbeiro.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>
  /* AJUSTES ESPECÍFICOS PARA MEUS AGENDAMENTOS */
  main {
    padding: 40px 80px;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* TÍTULO DA PÁGINA */
  .titulo-pagina {
    color: #333;
    font-size: 2rem;
    margin-bottom: 30px;
    font-weight: 600;
    text-align: center;
    font-family: "Poppins", sans-serif;
  }

  /* TABELA DE AGENDAMENTOS */
  .tabela-agendamentos {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
  }

  .tabela-agendamentos th {
    background-color: #FFB22C;
    color: #333;
    padding: 15px;
    text-align: left;
    font-weight: 500;
    font-family: "Poppins", sans-serif;
  }

  .tabela-agendamentos td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    color: #333;
    font-family: Arial, sans-serif;
  }

  .tabela-agendamentos tr:last-child td {
    border-bottom: none;
  }

  .tabela-agendamentos tr:hover {
    background-color: #fffaf0;
  }

  /* STATUS */
  .status {
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 4px;
    display: inline-block;
  }

  .status-pendente {
    color: #e67e22;
    background-color: #fef5e7;
  }

  .status-confirmado {
    color: #27ae60;
    background-color: #e8f8ef;
  }

  .status-cancelado {
    color: #e74c3c;
    background-color: #fdedec;
  }

  /* BOTÕES */
  .btn-cancelar {
    padding: 8px 16px;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    transition: all 0.3s;
    font-family: "Poppins", sans-serif;
  }

  .btn-cancelar:hover {
    background-color: #c0392b;
  }

  /* MENSAGENS */
  .mensagem-flash {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.95em;
  }

  .mensagem-sucesso {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  /* RESPONSIVO */
  @media (max-width: 768px) {
    main {
      padding: 20px;
    }

    .tabela-agendamentos {
      display: block;
      overflow-x: auto;
    }

    .tabela-agendamentos th,
    .tabela-agendamentos td {
      padding: 10px;
    }
  }
</style>

</head>
<body>

  <?php if (isset($_SESSION['mensagem'])): ?>
    <div style="text-align: center; margin: 20px 0;">
      <div class="mensagem-flash mensagem-sucesso">
          <?= htmlspecialchars($_SESSION['mensagem']) ?>
      </div>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
  <?php endif; ?>

  <main>
    <section class="agenda">
      <h2>Meus Agendamentos</h2>
      
      <?php if (empty($agendamentos)): ?>
        <p>Você não possui agendamentos marcados.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Profissional</th>
              <th>Serviço</th>
              <th>Data e Hora</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($agendamentos as $agendamento): ?>
              <tr>
                <td><?= htmlspecialchars($agendamento['profissional_nome']) ?></td>
                <td><?= htmlspecialchars($agendamento['servico_nome']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($agendamento['data_hora_inicio'])) ?></td>
                <td class="<?php
                    $agora = new DateTime();
                    $dataAgendamento = new DateTime($agendamento['data_hora_inicio']);

                    if ($agendamento['status'] === 'cancelado') {
                        echo 'status-cancelado';
                    } elseif ($agora < $dataAgendamento) {
                        echo 'status-confirmado';
                    } else {
                        echo 'status-concluido';
                    }
                ?>">
                    <?php
                        if ($agendamento['status'] === 'cancelado') {
                            echo 'cancelado';
                        } elseif ($agora < $dataAgendamento) {
                            echo 'confirmado';
                        } else {
                            echo 'concluído';
                        }
                    ?>
                </td>

                <td>
                  <?php if ($agendamento['status'] !== 'cancelado'): ?>
                    <form method="POST" action="meus_agendamentos.php" style="display: inline;">
                      <input type="hidden" name="agendamento_id" value="<?= $agendamento['agendamento_id'] ?>">
                      <button type="submit" name="cancelar_agendamento" class="btn-cancelar">Cancelar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </main>
  
  <?php require_once '../partes/footer.php'; ?>
</body>
</html>