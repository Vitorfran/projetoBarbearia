<?php
session_start();
// Caminho para o arquivo database.php
// Se admin_agendamentos.php estiver em 'seu_projeto/admin/'
// e database.php estiver em 'seu_projeto/config/', use '../config/database.php'
require_once '../config/database.php';

// Ativar exibição de erros para depuração (REMOVA EM PRODUÇÃO!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- AUTHENTICATION AND AUTHORIZATION ---
// Verifica se o usuário está logado e se é um administrador.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para acessar esta página.";
    // Redireciona para a página de login.
    // Ajuste o caminho se a sua página de login não estiver em '../pages/login.php'
    header('Location: ../pages/login.php');
    exit;
}
// --- END AUTHENTICATION ---

// Lógica para Agendamentos (mantida do seu código original)
// Initialize filters
$filtro_status = $_GET['status'] ?? '';
$filtro_profissional = $_GET['profissional'] ?? '';
$filtro_data = $_GET['data'] ?? '';

// Base query to fetch all appointments with complete information
$query = "
    SELECT
        a.id as agendamento_id,
        u_cliente.nome as cliente_nome,
        u_profissional.nome as profissional_nome,
        s.nome as servico_nome,
        s.preco as servico_preco,
        a.data_hora_inicio,
        a.data_hora_fim,
        COALESCE(a.status, 'pendente') as status,
        a.criado_em as data_criacao
    FROM agendamentos a
    JOIN usuarios u_cliente ON a.cliente_id = u_cliente.id
    JOIN usuarios u_profissional ON a.profissional_id = u_profissional.id
    JOIN servicos s ON a.servico_id = s.id
    WHERE 1=1
";

$params = [];

// Apply filters if present
if ($filtro_status) {
    $query .= " AND COALESCE(a.status, 'pendente') = ?";
    $params[] = $filtro_status;
}

if ($filtro_profissional) {
    $query .= " AND a.profissional_id = ?";
    $params[] = $filtro_profissional;
}

if ($filtro_data) {
    $query .= " AND DATE(a.data_hora_inicio) = ?";
    $params[] = $filtro_data;
}

$query .= " ORDER BY a.data_hora_inicio DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll();

// Fetch professionals for the filter dropdown
$profissionais = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

// Possible statuses for the filter dropdown
$status_possiveis = ['pendente', 'confirmado', 'cancelado', 'concluído'];


// Lógica para Usuários (do seu código original, mas com foco na coleta do ID)
$stmt = $pdo->query("SELECT id, nome, email, tipo, criado_em, id_plano, admin FROM usuarios"); // Adicionado 'admin'
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exibir mensagens da sessão (se houver)
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
    <title>Painel Admin - Agendamentos e Usuários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;0,800;0,900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/imgs/header/Cortai.png" alt="Cortai">
            <h1>Painel Administrativo</h1>
        </div>
        <nav>
       <ul>
            <li><a href="tela_admin.php#agendamentos-section">Agendamentos</a></li>
            <li><a href="tela_admin.php#usuarios-section">Usuários</a></li>
            <li><a href="logout.php">Sair</a></li>
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
                <h2 class="h3">Gerenciar Agendamentos</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card p-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($status_possiveis as $status): ?>
                                    <option value="<?= $status ?>" <?= $filtro_status === $status ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="profissional" class="form-label">Profissional</label>
                            <select id="profissional" name="profissional" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($profissionais as $prof): ?>
                                    <option value="<?= $prof['id'] ?>" <?= $filtro_profissional == $prof['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prof['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" id="data" name="data" class="form-control" value="<?= htmlspecialchars($filtro_data) ?>">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                            <a href="tela_admin.php" class="btn btn-outline-secondary">Limpar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <section class='agendamentos-section' >
        <h1 class="mt-5"> <span>Todos os Agendamentos </span></h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Profissional</th>
                                        <th>Serviço</th>
                                        <th>Valor</th>
                                        <th>Data/Hora</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($agendamentos)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">Nenhum agendamento encontrado</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($agendamentos as $agendamento): ?>
                                            <tr>
                                                <td><?= $agendamento['agendamento_id'] ?></td>
                                                <td><?= htmlspecialchars($agendamento['cliente_nome']) ?></td>
                                                <td><?= htmlspecialchars($agendamento['profissional_nome']) ?></td>
                                                <td><?= htmlspecialchars($agendamento['servico_nome']) ?></td>
                                                <td>
                                                    <span class="badge badge-valor">R$ <?= number_format($agendamento['servico_preco'], 2, ',', '.') ?></span>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($agendamento['data_hora_inicio'])) ?>
                                                    <?php if ($agendamento['data_hora_fim']): ?>
                                                        <br><small>até <?= date('H:i', strtotime($agendamento['data_hora_fim'])) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?= strtolower($agendamento['status']) ?>">
                                                        <?= ucfirst($agendamento['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="admin/admin_editar_agendamento.php?id=<?= $agendamento['agendamento_id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                        <form method="POST" action="admin/admin_alterar_status.php" class="d-inline">
                                                            <input type="hidden" name="id" value="<?= $agendamento['agendamento_id'] ?>">
                                                            <button type="submit" name="acao" value="cancelar" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');">Cancelar</button>
                                                            <?php if ($agendamento['status'] === 'pendente'): ?>
                                                                <button type="submit" name="acao" value="confirmar" class="btn btn-sm btn-outline-success" onclick="return confirm('Tem certeza que deseja confirmar este agendamento?');">Confirmar</button>
                                                            <?php endif; ?>
                                                            <?php if ($agendamento['status'] === 'confirmado'): ?>
                                                                <button type="submit" name="acao" value="concluir" class="btn btn-sm btn-outline-info" onclick="return confirm('Tem certeza que deseja marcar este agendamento como concluído?');">Concluir</button>
                                                            <?php endif; ?>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
        
        <h1 class="mt-5">Usuários</h1>
        <div class="row" id="user-section">
            <div class="col-md-8 col-lg-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Usuários</h5>
                        <a href="admin_adicionar_usuario.php" class="btn btn-success btn-sm">
                            <i class="bi bi-person-plus"></i> Adicionar Novo Usuário
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Membro Desde</th>
                                        <th>Plano</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($usuarios)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">Nenhum usuário encontrado</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?= $usuario['id'] ?></td>
                                                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $usuario['admin'] == 1 ? 'danger' : ($usuario['tipo'] == 'profissional' ? 'info' : 'secondary') ?>">
                                                        <?= $usuario['admin'] == 1 ? 'Admin' : ucfirst($usuario['tipo']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($usuario['criado_em'])) ?></td>
                                                <td>
                                                    <?php
                                                    // Fetch plano name if id_plano exists
                                                    $plano_nome = 'N/A';
                                                    if ($usuario['id_plano']) {
                                                        $stmt_plano = $pdo->prepare("SELECT nome FROM planos WHERE id_plano = ?");
                                                        $stmt_plano->execute([$usuario['id_plano']]);
                                                        $plano = $stmt_plano->fetch(PDO::FETCH_ASSOC);
                                                        if ($plano) {
                                                            $plano_nome = htmlspecialchars($plano['nome']);
                                                        }
                                                    }
                                                    ?>
                                                    <?= $plano_nome ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="admin/admin_editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar Usuário">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remover"
                                                            data-id="<?= $usuario['id'] ?>"  data-nome="<?= htmlspecialchars($usuario['nome']) ?>"
                                                            data-email="<?= htmlspecialchars($usuario['email']) ?>"
                                                            data-is-admin="<?= $usuario['admin'] ?>" title="Remover Usuário">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card border-danger" id="card-remover" style="display: none;">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close btn-close-white" aria-label="Close" id="btn-fechar-confirmacao"></button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i> Esta ação é irreversível!
                        </div>
                        <p>Você está prestes a excluir o usuário:</p>
                        <div class="mb-3 p-3 bg-light rounded">
                            <strong>ID:</strong> <span id="remover-id" class="badge bg-secondary"></span><br>
                            <strong>Nome:</strong> <span id="remover-nome" class="fw-bold"></span><br>
                            <strong>Email:</strong> <span id="remover-email"></span><br>
                            <strong>Tipo:</strong> <span id="remover-tipo" class="badge"></span>
                        </div>
                        <form method="POST" action="admin/admin_remover_usuario.php" id="form-remover">
                            <input type="hidden" name="id_usuario" id="input-remover-id"> <div class="mb-3">
                                <label for="confirmacao-texto" class="form-label">Para confirmar, digite <strong>CONFIRMAR</strong>:</label>
                                <input type="text" class="form-control" id="confirmacao-texto" name="confirmacao" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="bi bi-trash-fill"></i> Confirmar Exclusão
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-cancelar">
                                    <i class="bi bi-x-lg"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cardRemover = document.getElementById('card-remover');
            const formRemover = document.getElementById('form-remover'); // Formulário de exclusão dentro do card

            // Esconde o card de confirmação por padrão
            cardRemover.style.display = 'none';

            // Adiciona evento de clique a TODOS os botões "Remover" na tabela de usuários
            document.querySelectorAll('.btn-remover').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Preenche os detalhes no card de confirmação
                    const userId = this.dataset.id;
                    const userName = this.dataset.nome;
                    const userEmail = this.dataset.email;
                    const userIsAdmin = this.dataset.isAdmin; // Pega o status de admin

                    document.getElementById('remover-id').textContent = userId;
                    document.getElementById('remover-nome').textContent = userName;
                    document.getElementById('remover-email').textContent = userEmail;

                    // Ajusta o badge de tipo no card de confirmação
                    const removerTipoBadge = document.getElementById('remover-tipo');
                    if (userIsAdmin === '1') { // Cuidado com tipos de dados de dataset (string)
                        removerTipoBadge.textContent = 'Administrador';
                        removerTipoBadge.classList.remove('bg-secondary', 'bg-info');
                        removerTipoBadge.classList.add('bg-danger');
                    } else {
                        // Você pode adicionar mais lógica aqui se tiver outros tipos como 'profissional'
                        removerTipoBadge.textContent = 'Cliente'; // Ou outro tipo
                        removerTipoBadge.classList.remove('bg-danger', 'bg-info');
                        removerTipoBadge.classList.add('bg-secondary');
                    }


                    // ESSENCIAL: PREENCHE O INPUT HIDDEN COM O ID DO USUÁRIO
                    document.getElementById('input-remover-id').value = userId;

                    // Limpa o campo de confirmação de texto
                    document.getElementById('confirmacao-texto').value = '';

                    // Destaca a linha do usuário que será removido na tabela
                    document.querySelectorAll('tbody tr').forEach(r => r.classList.remove('table-danger'));
                    this.closest('tr').classList.add('table-danger');

                    // Exibe o card de confirmação
                    cardRemover.style.display = 'block';
                });
            });

            // Lógica para esconder o card de confirmação
            function hideCard() {
                cardRemover.style.display = 'none';
                document.querySelectorAll('tbody tr').forEach(r => r.classList.remove('table-danger')); // Remove destaque
            }

            document.querySelector('.btn-cancelar').addEventListener('click', hideCard);
            document.getElementById('btn-fechar-confirmacao').addEventListener('click', hideCard);

            // Validação de confirmação no lado do cliente (melhora a UX)
            formRemover.addEventListener('submit', function(e) {
                const confirmacaoTexto = document.getElementById('confirmacao-texto').value;
                if (confirmacaoTexto.toUpperCase() !== 'CONFIRMAR') {
                    e.preventDefault(); // Impede o envio do formulário
                    alert('Por favor, digite "CONFIRMAR" exatamente para prosseguir.');
                    document.getElementById('confirmacao-texto').focus();
                } else {
                    // Adicionalmente, uma confirmação final antes de enviar
                    if (!confirm('Tem certeza absoluta? Esta ação não pode ser desfeita!')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>