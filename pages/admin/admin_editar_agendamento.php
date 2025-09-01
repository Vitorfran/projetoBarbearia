<?php
session_start();
require_once '../../config/database.php'; // Ajuste o caminho conforme necessário

// Ativar exibição de erros para depuração (REMOVA EM PRODUÇÃO!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- AUTHENTICATION AND AUTHORIZATION ---
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para acessar esta página.";
    header('Location: ../login.php'); // Ajuste o caminho se necessário
    exit;
}
// --- END AUTHENTICATION ---

$agendamento = null;
$profissionais = [];
$servicos = [];
$status_possiveis = ['pendente', 'confirmado', 'cancelado', 'concluído']; // Defina os status possíveis

// Lógica para carregar os dados do agendamento e preencher o formulário
if (isset($_GET['id'])) {
    $agendamento_id = $_GET['id'];

    // Buscar agendamento
    $stmt = $pdo->prepare("
        SELECT
            a.id as agendamento_id,
            a.cliente_id,
            u_cliente.nome as cliente_nome,
            a.profissional_id,
            u_profissional.nome as profissional_nome,
            a.servico_id,
            s.nome as servico_nome,
            s.preco as servico_preco,
            a.data_hora_inicio,
            a.data_hora_fim,
            COALESCE(a.status, 'pendente') as status
        FROM agendamentos a
        JOIN usuarios u_cliente ON a.cliente_id = u_cliente.id
        JOIN usuarios u_profissional ON a.profissional_id = u_profissional.id
        JOIN servicos s ON a.servico_id = s.id
        WHERE a.id = ?
    ");
    $stmt->execute([$agendamento_id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        $_SESSION['mensagem'] = "Agendamento não encontrado.";
        header('Location: ../tela_admin.php'); // Redireciona de volta
        exit;
    }

    // Buscar profissionais (tipo 'profissional')
    $stmt = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional' ORDER BY nome");
    $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar serviços
    $stmt = $pdo->query("SELECT id, nome, preco FROM servicos ORDER BY nome");
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para processar o formulário de edição
    $agendamento_id = $_POST['agendamento_id'] ?? null;
    $novo_cliente_id = $_POST['cliente_id'] ?? null; // Você pode permitir mudar o cliente, se quiser
    $novo_profissional_id = $_POST['profissional_id'] ?? null;
    $novo_servico_id = $_POST['servico_id'] ?? null;
    $nova_data_hora_inicio = $_POST['data_hora_inicio'] ?? null;
    $nova_data_hora_fim = $_POST['data_hora_fim'] ?? null;
    $novo_status = $_POST['status'] ?? null;

    // Validação básica (adicione mais validação conforme necessário)
    if (empty($agendamento_id) || empty($novo_profissional_id) || empty($novo_servico_id) || empty($nova_data_hora_inicio) || empty($novo_status)) {
        $_SESSION['mensagem'] = "Erro: Campos obrigatórios não preenchidos.";
        header('Location: admin_editar_agendamento.php?id=' . $agendamento_id); // Redireciona de volta para a edição
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE agendamentos
            SET
                profissional_id = ?,
                servico_id = ?,
                data_hora_inicio = ?,
                data_hora_fim = ?,
                status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $novo_profissional_id,
            $novo_servico_id,
            $nova_data_hora_inicio,
            $nova_data_hora_fim,
            $novo_status,
            $agendamento_id
        ]);

        $_SESSION['mensagem'] = "Agendamento atualizado com sucesso!";
        header('Location: ../tela_admin.php'); // Redireciona para a lista principal
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao atualizar agendamento: " . $e->getMessage();
        header('Location: admin_editar_agendamento.php?id=' . $agendamento_id); // Redireciona de volta para a edição
        exit;
    }
} else {
    $_SESSION['mensagem'] = "ID do agendamento não fornecido.";
    header('Location: ../admin_agendamentos.php'); // Redireciona de volta
    exit;
}

// Exibir mensagens da sessão
$mensagem = '';
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Agendamento - Painel Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../../assets/imgs/header/Cortai.png" alt="Cortai"> <h1>Painel Administrativo</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../tela_admin.php#agendamentos-section">Agendamentos</a></li>
                <li><a href="../tela_admin.php#usuarios-section">Usuários</a></li>
                <li><a href="../tela_admin.php#servicos-section">Serviços</a></li>
                <li><a href="../logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main class="container-fluid py-4">
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?= strpos($mensagem, 'Erro') !== false ? 'erro' : 'sucesso' ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3">Editar Agendamento #<?= htmlspecialchars($agendamento['agendamento_id']) ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card p-4">
                    <form action="admin_editar_agendamento.php" method="POST">
                        <input type="hidden" name="agendamento_id" value="<?= htmlspecialchars($agendamento['agendamento_id']) ?>">

                        <div class="mb-3">
                            <label for="cliente_nome" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente_nome" value="<?= htmlspecialchars($agendamento['cliente_nome']) ?>" readonly>
                            <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($agendamento['cliente_id']) ?>">
                            <small class="form-text text-muted">O cliente não pode ser alterado diretamente aqui. Para mudar o cliente, crie um novo agendamento.</small>
                        </div>

                        <div class="mb-3">
                            <label for="profissional_id" class="form-label">Profissional</label>
                            <select class="form-select" id="profissional_id" name="profissional_id" required>
                                <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof['id'] ?>" <?= $agendamento['profissional_id'] == $prof['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prof['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="servico_id" class="form-label">Serviço</label>
                            <select class="form-select" id="servico_id" name="servico_id" required>
                                <?php foreach ($servicos as $serv): ?>
                                    <option value="<?= $serv['id'] ?>" <?= $agendamento['servico_id'] == $serv['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($serv['nome']) ?> (R$ <?= number_format($serv['preco'], 2, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data_hora_inicio" class="form-label">Data e Hora de Início</label>
                                <input type="datetime-local" class="form-control" id="data_hora_inicio" name="data_hora_inicio" value="<?= date('Y-m-d\TH:i', strtotime($agendamento['data_hora_inicio'])) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="data_hora_fim" class="form-label">Data e Hora de Fim (Opcional)</label>
                                <input type="datetime-local" class="form-control" id="data_hora_fim" name="data_hora_fim" value="<?= $agendamento['data_hora_fim'] ? date('Y-m-d\TH:i', strtotime($agendamento['data_hora_fim'])) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <?php foreach ($status_possiveis as $status): ?>
                                    <option value="<?= $status ?>" <?= $agendamento['status'] == $status ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="../tela_admin.php#agendamentos-section" class="btn btn-outline-secondary">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>