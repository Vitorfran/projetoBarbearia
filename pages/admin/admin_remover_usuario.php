<?php
session_start();
// Caminho para o arquivo database.php
// Se admin_excluir_usuario.php está em 'seu_projeto/pages/admin/'
// e database.php está em 'seu_projeto/config/', '../../config/database.php' está correto.
require_once '../../config/database.php';

// Ativar exibição de erros para depuração (REMOVA EM PRODUÇÃO!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- AUTENTICAÇÃO E AUTORIZAÇÃO ---
// Verifica se o usuário está logado e se é um administrador.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para acessar esta página.";
    // Redireciona para a página de login.
    // Saindo de 'pages/admin/' para 'pages/login.php', basta subir um nível.
    header('Location: ../login.php'); // CORRIGIDO: Agora aponta corretamente para 'pages/login.php'
    exit;
}
// --- FIM DA AUTENTICAÇÃO ---

$usuario_id = null;
$usuario = null;
$mensagem = '';

// --- PASSO 1: CARREGAR DADOS DO USUÁRIO PARA CONFIRMAÇÃO (Requisição GET) ---
// Este bloco é executado quando a página é carregada pela primeira vez,
// geralmente através de um link como 'admin_excluir_usuario.php?id=XXX'
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $usuario_id = $_GET['id']; // Pega o ID da URL

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, admin FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            // Se o usuário não for encontrado, define uma mensagem e redireciona
            $_SESSION['mensagem'] = "Erro: Usuário não encontrado para exclusão.";
            // Saindo de 'pages/admin/' para 'pages/tela_admin.php', basta subir um nível.
            header('Location: ../tela_admin.php'); // CORRIGIDO: Agora aponta para 'pages/tela_admin.php'
            exit;
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao carregar usuário: " . $e->getMessage();
        error_log("Erro ao carregar usuário: " . $e->getMessage()); // Registra o erro no log
    }
}

// --- PASSO 2: PROCESSAR A EXCLUSÃO (Requisição POST) ---
// Este bloco é executado quando o formulário de confirmação é enviado.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $usuario_id = $_POST['id_usuario']; // Pega o ID do campo hidden do formulário POST
    $confirmacao = trim($_POST['confirmacao']);

    // Verifica se a confirmação "CONFIRMAR" foi digitada corretamente
    if (strtoupper($confirmacao) !== 'CONFIRMAR') {
        $mensagem = "Erro: Você deve digitar 'CONFIRMAR' para excluir o usuário.";
        // Opcional: Recarregar os dados do usuário para reexibir o formulário
        try {
            $stmt = $pdo->prepare("SELECT id, nome, email, admin FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao recarregar usuário após falha na confirmação: " . $e->getMessage());
            $usuario = null; // Para não exibir dados inconsistentes
        }
    } else {
        try {
            // Verifica se é o último administrador no sistema
            $stmt = $pdo->prepare("SELECT COUNT(*) as total_admins FROM usuarios WHERE admin = 1");
            $stmt->execute();
            $total_admins = $stmt->fetch(PDO::FETCH_ASSOC)['total_admins'];

            // Pega o status de admin do usuário que será excluído
            $stmt = $pdo->prepare("SELECT admin FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $usuario_a_excluir_info = $stmt->fetch(PDO::FETCH_ASSOC);

            // Regra: Não permitir excluir o último administrador
            if ($usuario_a_excluir_info && $usuario_a_excluir_info['admin'] == 1 && $total_admins <= 1) {
                $mensagem = "Erro: Não é possível excluir o último administrador do sistema.";
                // Opcional: Recarregar os dados do usuário para reexibir o formulário
                try {
                    $stmt = $pdo->prepare("SELECT id, nome, email, admin FROM usuarios WHERE id = ?");
                    $stmt->execute([$usuario_id]);
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    error_log("Erro ao recarregar usuário após tentativa de excluir último admin: " . $e->getMessage());
                    $usuario = null;
                }
            } else {
                // Excluir usuário
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$usuario_id]);

                $_SESSION['mensagem'] = "Usuário excluído com sucesso!";
                // Redireciona de volta para a tela de admin de usuários
                // Saindo de 'pages/admin/' para 'pages/tela_admin.php', basta subir um nível.
                header('Location: ../tela_admin.php'); // CORRIGIDO: Agora aponta para 'pages/tela_admin.php'
                exit;
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir usuário: " . $e->getMessage();
            error_log("Erro ao excluir usuário: " . $e->getMessage());
        }
    }
}

// Exibir mensagens da sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h2>Excluir Usuário</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-<?= strpos($mensagem, 'sucesso') !== false ? 'success' : 'danger' ?>" role="alert">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <?php if ($usuario): ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Atenção!</h4>
                        <p>Você está prestes a excluir permanentemente o usuário abaixo:</p>
                        <hr>
                        <p class="mb-0">
                            <strong>ID:</strong> <?= htmlspecialchars($usuario['id']) ?><br>
                            <strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?><br>
                            <strong>Tipo:</strong> <?= $usuario['admin'] == 1 ? 'Administrador' : 'Cliente' ?>
                        </p>
                    </div>

                    <form action="admin_excluir_usuario.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id']) ?>">

                        <div class="mb-3">
                            <label for="confirmacao" class="form-label">Para confirmar, digite <strong>CONFIRMAR</strong>:</label>
                            <input type="text" class="form-control" id="confirmacao" name="confirmacao" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="../tela_admin.php" class="btn btn-secondary me-md-2">Cancelar</a> <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza absoluta? Esta ação não pode ser desfeita!')">
                                <i class="bi bi-trash-fill"></i> Excluir Permanentemente
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Carregando dados do usuário...</p>
                    <?php if (!isset($_GET['id'])): ?>
                        <p>ID do usuário não fornecido na URL.</p>
                        <a href="../tela_admin.php" class="btn btn-secondary">Voltar para a lista</a> <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const confirmacao = document.getElementById('confirmacao').value;
            if (confirmacao.toUpperCase() !== 'CONFIRMAR') {
                e.preventDefault();
                alert('Por favor, digite "CONFIRMAR" exatamente como mostrado para confirmar a exclusão.');
                document.getElementById('confirmacao').focus();
            }
        });
    </script>
</body>
</html>