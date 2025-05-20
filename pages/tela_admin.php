<?php
session_start();
require_once '../config/database.php';

// Uncomment and use this block if you want to enforce admin login
// if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['admin'])) {
//     header('Location: ../pages/login.php');
//     exit;
// }


$stmt = $pdo->query("SELECT id, nome, email, tipo, criado_em, id_plano FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel Admin - Agendamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/imgs/header/Cortai.png" alt="Cortai">     
            <h1>Painel Administrativo</h1>
        </div>
        <nav>
            <ul>
                <li><a href="admin_agendamentos.php">Agendamentos</a></li>
                <li><a href="admin_usuarios.php">Usuários</a></li>
                <li><a href="admin_servicos.php">Serviços</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>
    <main class="container-fluid py-4">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="mensagem <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'erro' : 'sucesso' ?>">
                <?= htmlspecialchars($_SESSION['mensagem']) ?>
            </div>
            <?php unset($_SESSION['mensagem']); // Clear the message after displaying ?>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col">
                    <h2 class="h3">Gerenciar Agendamentos</h2>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col">
                    <div class="filter-card">
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
                            <a href="admin_agendamentos.php" class="btn btn-outline-secondary">Limpar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <h1>Todos os Agendamentos</h1>
        <div class="row">
            <div class="col">
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
                                                        <a href="admin_editar_agendamento.php?id=<?= $agendamento['agendamento_id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                        <form method="POST" action="admin_alterar_status.php" class="d-inline">
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


        <!-- sessao 2 -->

    <h1>Usuarios</h1>                                                            
    <div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>tipo</th>
                                <th>Criado em</th>
                                <th>Plano</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Nenhum Usuário Disponível</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= htmlspecialchars($usuario['tipo']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></td>
                                        <td><?= htmlspecialchars($usuario['id_plano']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="../pages/admin/admin_editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                <form method="POST" action="admin_deletar_usuario.php" class="d-inline">
                                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                                    <button type="submit" name="acao" value="deletar" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja apagar este usuário?');">Apagar</button>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>