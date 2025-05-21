<?php
session_start();
require_once '../../config/database.php';// Correct path to your database connection

// --- IMPORTANT: AUTHENTICATION AND AUTHORIZATION ---
// This is crucial. Ensure your $_SESSION['usuario'] has the 'admin' flag or 'tipo'
// For example, if your 'tipo' column determines admin status:
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para acessar esta página.";
    header('Location: ../pages/login.php');
    exit;
}
// }
// --- END AUTHENTICATION ---

$usuario_id = null;
$usuario = null;
$mensagem = ''; // For displaying messages on this page

// 1. Logic to load user data (GET request)
// This happens when you click "Editar" from the user list
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, tipo, id_plano FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['mensagem'] = "Usuário não encontrado.";
            header('Location: tela_admin.php'); // Redirect back to the user list
            exit;
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao carregar usuário: " . $e->getMessage();
        error_log("Erro ao carregar usuário: " . $e->getMessage()); // Log the error for debugging
    }
}

// 2. Logic to process user update (POST request)
// This happens when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $usuario_id = $_POST['id_usuario'];
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);
    $novo_tipo = trim($_POST['tipo']); // Assuming 'tipo' can be updated
    $novo_plano = trim($_POST['id_plano']); // Assuming 'id_plano' can be updated

    // Basic validation
    if (empty($novo_nome) || empty($novo_email) || empty($novo_tipo) || empty($novo_plano)) {
        $mensagem = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Formato de e-mail inválido.";
    } else {
        try {
            // Prepare and execute the update query
            // IMPORTANT: If 'id_plano' is a FOREIGN KEY, the value must exist in the 'planos' table
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ?, id_plano = ? WHERE id = ?");
            $stmt->execute([$novo_nome, $novo_email, $novo_tipo, $novo_plano, $usuario_id]);

            if ($stmt->rowCount()) {
                $_SESSION['mensagem'] = "Usuário atualizado com sucesso!";
                header('Location: /projetoBarbearia/pages/tela_admin.php'); // Redirect back to the user list
                exit;
            } else {
                $mensagem = "Nenhuma alteração foi feita ou usuário não encontrado.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar usuário: " . $e->getMessage();
            error_log("Erro ao atualizar usuário: " . $e->getMessage()); // Log the error
        }
    }
    // Re-populate $usuario in case of POST submission error
    $usuario = [
        'id' => $usuario_id,
        'nome' => $novo_nome,
        'email' => $novo_email,
        'tipo' => $novo_tipo,
        'id_plano' => $novo_plano
    ];
}

// Display messages from $_SESSION (if any)
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']); // Clear message after display
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css"> </head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Editar Usuário</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-info" role="alert">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <?php if ($usuario): ?>
                    <form action="admin_editar_usuario.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id']) ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo:</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="cliente" <?= ($usuario['tipo'] === 'cliente') ? 'selected' : '' ?>>Cliente</option>
                                <option value="profissional" <?= ($usuario['tipo'] === 'profissional') ? 'selected' : '' ?>>Profissional</option>
                                <option value="admin" <?= ($usuario['tipo'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_plano" class="form-label">ID do Plano:</label>
                            <input type="text" class="form-control" id="id_plano" name="id_plano" value="<?= htmlspecialchars($usuario['id_plano']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <a href="/projetoBarbearia/pages/tela_admin.php" class="btn btn-secondary">Voltar</a>
                    </form>
                <?php else: ?>
                    <p>Carregando dados do usuário...</p>
                    <?php if (!isset($_GET['id'])): ?>
                        <p>ID do usuário não fornecido.</p>
                        <a href="/projetoBarbearia/pages/tela_admin.php" class="btn btn-secondary">Voltar para a lista</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>